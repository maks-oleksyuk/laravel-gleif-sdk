<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Oleksyuk\Gleif\GleifConnector;
use Oleksyuk\Gleif\GleifRequest;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\LeiRecordById;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Parents\DirectParentLeiRecord;
use Saloon\CachePlugin\Helpers\CacheKeyHelper;
use Saloon\Exceptions\Request\Statuses\NotFoundException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

/**
 * The TTL the response was stored with, or null when it isn't cached.
 */
function cachedTtl(Response $response): ?int
{
    $request = $response->getRequest();
    assert($request instanceof GleifRequest);

    return $request->resolveCacheDriver()->get(CacheKeyHelper::createHashed($response->getPendingRequest()))?->ttl;
}

test('repeated requests are served from cache and do not count against the rate limit', function (): void {
    $mockClient = new MockClient([
        LeiRecordById::class => MockResponse::make(['data' => ['id' => 'LEI', 'attributes' => ['lei' => 'LEI']]]),
    ]);

    $connector = new GleifConnector()->withMockClient($mockClient);

    $first = $connector->leiRecord()->find('LEI');
    $second = $connector->leiRecord()->find('LEI');

    expect($first->isCached())->toBeFalse()
        ->and($second->isCached())->toBeTrue()
        ->and($second->json('data.id'))->toBe('LEI');

    $mockClient->assertSentCount(1);

    $limit = $connector->getLimits()[0]->update($connector->rateLimitStore());
    expect($limit->getHits())->toBe(1);
});

test('different requests are cached separately', function (): void {
    $mockClient = new MockClient([
        LeiRecordById::class => MockResponse::make(['data' => []]),
    ]);

    $connector = new GleifConnector()->withMockClient($mockClient);

    $connector->leiRecord()->find('A');
    $connector->leiRecord()->find('B');
    $connector->leiRecord()->find('A');

    $mockClient->assertSentCount(2);
});

test('the gleif.cache.store config selects the cache store', function (): void {
    config()->set('cache.stores.gleif', ['driver' => 'array']);
    config()->set('gleif.cache.store', 'gleif');

    $mockClient = new MockClient([
        LeiRecordById::class => MockResponse::make(['data' => []]),
    ]);

    new GleifConnector()->withMockClient($mockClient)->leiRecord()->find('A');

    $limit = new GleifConnector()->getLimits()[0];

    expect(cache()->store('gleif')->get($limit->getName()))->not->toBeNull()
        ->and(cache()->store('array')->get($limit->getName()))->toBeNull();
});

test('a 404 is cached like any other answer and does not count against the rate limit', function (): void {
    $mockClient = new MockClient([
        DirectParentLeiRecord::class => MockResponse::make(['errors' => [['status' => '404']]], 404),
    ]);

    $connector = new GleifConnector()->withMockClient($mockClient);

    $first = $connector->leiRecord()->directParent('LEI');
    $second = $connector->leiRecord()->directParent('LEI');

    expect($first->isCached())->toBeFalse()
        ->and($second->isCached())->toBeTrue()
        ->and($second->status())->toBe(404)
        ->and($second->dto(...))->toThrow(NotFoundException::class)
        ->and($connector->getLimits()[0]->update($connector->rateLimitStore())->getHits())->toBe(1);

    $mockClient->assertSentCount(1);
});

test('an answer from the latest published golden copy is cached until the next publication, an older one for 15 minutes', function (string $now, string $publishDate, int $ttl): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse($now, 'UTC'));

    $mockClient = new MockClient([
        LeiRecordById::class => MockResponse::make(['data' => [], 'meta' => ['goldenCopy' => ['publishDate' => $publishDate]]]),
    ]);

    $response = new GleifConnector()->withMockClient($mockClient)->leiRecord()->find('X');

    expect(cachedTtl($response))->toBe($ttl);
})->with([
    // The 08:00 copy is published at 10:00, and the API catches up hours later.
    'the 00:00 copy before the 08:00 one is out' => ['2026-01-01 09:00:00', '2026-01-01T00:00:00Z', 3600],
    'still the 00:00 copy after the 08:00 one is out' => ['2026-01-01 11:00:00', '2026-01-01T00:00:00Z', 15 * 60],
    'already the 08:00 copy' => ['2026-01-01 11:00:00', '2026-01-01T08:00:00Z', 7 * 3600],
]);

test('a response without a golden copy date, even one that is not JSON, is cached until the next publication', function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-01-01 11:00:00', 'UTC'));

    $mockClient = new MockClient([LeiRecordById::class => MockResponse::make('not json')]);

    $response = new GleifConnector()->withMockClient($mockClient)->leiRecord()->find('X');

    expect(cachedTtl($response))->toBe(7 * 3600);
});

test('disabling the cache also skips caching a 404', function (): void {
    $mockClient = new MockClient([
        DirectParentLeiRecord::class => MockResponse::make([], 404),
    ]);

    $connector = new GleifConnector()->withMockClient($mockClient);

    $connector->send(new DirectParentLeiRecord('LEI')->disableCaching());
    $connector->send(new DirectParentLeiRecord('LEI')->disableCaching());

    $mockClient->assertSentCount(2);
});
