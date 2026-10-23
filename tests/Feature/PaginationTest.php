<?php

declare(strict_types=1);

use Oleksyuk\Gleif\GleifConnector;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\AllLeiRecords;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;

test('paginate() starts at page 1 and stops when links.next is missing', function (): void {
    $mockClient = new MockClient([
        AllLeiRecords::class => fn (PendingRequest $request): MockResponse => match ($request->query()->get('page[number]')) {
            1 => MockResponse::make(['data' => [['id' => 'A'], ['id' => 'B']], 'links' => ['next' => 'page-2']]),
            2 => MockResponse::make(['data' => [['id' => 'C']], 'links' => []]),
            default => MockResponse::make([], 500),
        },
    ]);

    $paginator = new GleifConnector()->withMockClient($mockClient)->paginate(new AllLeiRecords)->setPerPageLimit(2);

    $ids = [];

    foreach ($paginator->items() as $item) {
        $ids[] = data_get($item, 'id');
    }

    expect($ids)->toBe(['A', 'B', 'C']);
    $mockClient->assertSentCount(2);
    $mockClient->assertSent(fn (AllLeiRecords $request): bool => $request->query()->get('page[size]') === 2);
});
