<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\LeiRecordById;

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('cache expires at the next golden copy publication', function (string $now, int $expected): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse($now, 'UTC'));

    expect(new LeiRecordById('X')->cacheExpiryInSeconds())->toBe($expected);
})->with([
    'right when the 00:00 copy is out' => ['2026-01-01 02:00:00', 8 * 3600],
    'before the 08:00 copy is out' => ['2026-01-01 09:15:00', 45 * 60],
    'between the 10:00 and 18:00 releases' => ['2026-01-01 11:00:00', 7 * 3600],
    'after the 18:00 release rolls over to tomorrow' => ['2026-01-01 23:15:00', 2 * 3600 + 45 * 60],
    'after midnight, before the 00:00 copy is out' => ['2026-01-02 01:15:00', 45 * 60],
]);
