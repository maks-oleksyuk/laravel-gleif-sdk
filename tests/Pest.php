<?php

declare(strict_types=1);

use Oleksyuk\Gleif\Tests\TestCase;

pest()->extend(TestCase::class)->in('Feature', 'Unit');

/**
 * A real GLEIF API resource object, as saved in tests/Fixtures.
 *
 * @return array<string, mixed>
 */
function gleifFixture(string $name): array
{
    /** @var array<string, mixed> */
    return json_decode((string) file_get_contents(__DIR__."/Fixtures/{$name}.json"), true, flags: JSON_THROW_ON_ERROR);
}
