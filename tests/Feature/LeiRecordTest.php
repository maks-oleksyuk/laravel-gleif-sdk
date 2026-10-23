<?php

declare(strict_types=1);

use Oleksyuk\Gleif\Data\LeiRecord;
use Oleksyuk\Gleif\Enums\EntityCategory;
use Oleksyuk\Gleif\Enums\RegistrationStatus;
use Oleksyuk\Gleif\GleifConnector;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\AllLeiRecords;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\LeiRecordById;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

const GLEIF_LEI = '984500A8D612DCC67997';

/**
 * @return array<string, mixed>
 */
function leiRecordPayload(): array
{
    return [
        'id' => GLEIF_LEI,
        'type' => 'lei-records',
        'attributes' => [
            'lei' => GLEIF_LEI,
            'entity' => [
                'legalName' => ['name' => 'Global Legal Entity Identifier Foundation', 'language' => 'en'],
                'category' => 'GENERAL',
            ],
            'registration' => [
                'status' => 'ISSUED',
                'initialRegistrationDate' => '2014-12-10T00:00:00Z',
            ],
            'bic' => ['GLEICHZZXXX', null],
            'conformityFlag' => 'CONFORMING',
        ],
    ];
}

test('all() filters by lei and maps records to DTOs', function (): void {
    $mockClient = new MockClient([
        AllLeiRecords::class => MockResponse::make(['data' => [leiRecordPayload()]]),
    ]);

    $records = new GleifConnector()->withMockClient($mockClient)->leiRecord()->all(lei: [GLEIF_LEI, 'OTHER'])->dto();

    $mockClient->assertSent(fn (AllLeiRecords $request): bool => $request->query()->get('filter[lei]') === GLEIF_LEI.',OTHER');

    expect($records)->toBeArray()->toHaveCount(1)->toContainOnlyInstancesOf(LeiRecord::class);
});

test('find() maps a single record', function (): void {
    $mockClient = new MockClient([
        LeiRecordById::class => MockResponse::make(['data' => leiRecordPayload()]),
    ]);

    $record = new GleifConnector()->withMockClient($mockClient)->leiRecord()->find(GLEIF_LEI)->dto();

    expect($record)->toBeInstanceOf(LeiRecord::class);
    assert($record instanceof LeiRecord);

    expect($record->lei)->toBe(GLEIF_LEI)
        ->and($record->entityLegalName)->toBe(['name' => 'Global Legal Entity Identifier Foundation', 'language' => 'en'])
        ->and($record->entityCategory)->toBe(EntityCategory::General)
        ->and($record->registrationStatus)->toBe(RegistrationStatus::Issued)
        ->and($record->registrationInitialRegistrationDate?->toDateString())->toBe('2014-12-10')
        ->and($record->bic)->toBe(['GLEICHZZXXX'])
        ->and($record->entityStatus)->toBeNull();
});
