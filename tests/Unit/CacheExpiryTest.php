<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\LeiRecordById;

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('cache expires 15 minutes after the next golden copy publication', function (string $now, int $expected): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse($now, 'UTC'));

    expect(new LeiRecordById('X')->cacheExpiryInSeconds())->toBe($expected);
})->with([
    'before the 02:00 run' => ['2026-01-01 01:00:00', 75 * 60],
    'between 02:00 and 10:00' => ['2026-01-01 02:15:00', 8 * 3600],
    'between 10:00 and 18:00' => ['2026-01-01 17:15:00', 3600],
    'after the 18:00 run rolls over to tomorrow' => ['2026-01-01 23:15:00', 3 * 3600],
]);
