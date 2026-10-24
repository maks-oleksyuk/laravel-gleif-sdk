<?php

declare(strict_types=1);

use Oleksyuk\Gleif\Data\LeiRecord;
use Oleksyuk\Gleif\GleifConnector;
use Oleksyuk\Gleif\Requests\Countries\AllCountryCodes;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\AllLeiRecords;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;

/**
 * @param  iterable<mixed>  $items
 * @return list<mixed>
 */
function pageItemIds(iterable $items): array
{
    $ids = [];

    foreach ($items as $item) {
        $ids[] = data_get($item, 'id');
    }

    return $ids;
}

test('paginate() starts a cursor, follows links.next past the page-number limit and maps items to DTOs', function (): void {
    $mockClient = new MockClient([
        AllLeiRecords::class => fn (PendingRequest $request): MockResponse => match ($request->query()->get('page[cursor]')) {
            '*' => MockResponse::make([
                'data' => [['id' => 'A'], ['id' => 'B']],
                'links' => ['next' => 'https://api.gleif.org/api/v1/lei-records?filter%5Bfulltext%5D=x&page%5Bcursor%5D=c%2F2&page%5Bsize%5D=2'],
            ]),
            'c/2' => MockResponse::make(['data' => [['id' => 'C']], 'links' => []]),
            default => MockResponse::make([], 500),
        },
    ]);

    $paginator = new GleifConnector()->withMockClient($mockClient)
        ->paginate(new AllLeiRecords(fulltext: 'x', pageNumber: 5))
        ->setPerPageLimit(2);

    $items = iterator_to_array($paginator->items(), false);

    expect($items)->toContainOnlyInstancesOf(LeiRecord::class)
        ->and(pageItemIds($items))->toBe(['A', 'B', 'C']);

    $mockClient->assertSentCount(2);
    $mockClient->assertSent(fn (AllLeiRecords $request, Response $response): bool => $response->getPendingRequest()->query()->all() == [
        'filter[fulltext]' => 'x',
        'page[cursor]' => '*',
        'page[size]' => 2,
    ]);
});

test('paginate() follows page-number links on endpoints without cursors', function (): void {
    $mockClient = new MockClient([
        AllCountryCodes::class => fn (PendingRequest $request): MockResponse => match ($request->query()->get('page[number]')) {
            null => MockResponse::make([
                'data' => [['id' => 'AD']],
                'links' => ['next' => 'https://api.gleif.org/api/v1/countries?sort=code&page%5Bnumber%5D=2&page%5Bsize%5D=1'],
            ]),
            '2' => MockResponse::make(['data' => [['id' => 'AE']], 'links' => ['next' => null]]),
            default => MockResponse::make([], 500),
        },
    ]);

    $paginator = new GleifConnector()->withMockClient($mockClient)->paginate(new AllCountryCodes);

    expect(pageItemIds($paginator->items()))->toBe(['AD', 'AE']);
    $mockClient->assertSentCount(2);
});

test('paginate() treats a page without data as empty', function (): void {
    $mockClient = new MockClient([AllCountryCodes::class => MockResponse::make(['links' => []])]);

    expect(pageItemIds(new GleifConnector()->withMockClient($mockClient)->paginate(new AllCountryCodes)->items()))->toBeEmpty();
});
