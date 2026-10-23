<?php

declare(strict_types=1);

use Oleksyuk\Gleif\GleifConnector;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\LeiRecordById;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

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
