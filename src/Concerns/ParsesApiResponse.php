<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Concerns;

use Carbon\CarbonImmutable;

trait ParsesApiResponse
{
    /**
     * @param  array<mixed>  $data
     */
    protected static function asString(array $data, string $key, string $default = ''): string
    {
        $value = data_get($data, $key, $default);

        /** @phpstan-ignore cast.string */
        return is_scalar($value) ? (string) $value : $default;
    }

    /**
     * Null for a missing or empty value; "0" stays a value.
     *
     * @param  array<mixed>  $data
     */
    protected static function asNullableString(array $data, string $key): ?string
    {
        $value = self::asString($data, $key);

        return $value === '' ? null : $value;
    }

    /**
     * @param  array<mixed>  $data
     * @return array<string, mixed>
     */
    protected static function asArray(array $data, string $key): array
    {
        $value = data_get($data, $key, []);

        /** @var array<string, mixed> $result */
        $result = is_array($value) ? $value : [];

        return $result;
    }

    /**
     * Date-only values become midnight UTC, so they don't shift with the app timezone;
     * date-times keep the offset GLEIF sent. An empty value is null, not "now" as Carbon would parse it.
     *
     * @param  array<mixed>  $data
     */
    protected static function asDate(array $data, string $key): ?CarbonImmutable
    {
        $value = data_get($data, $key);

        return is_string($value) && $value !== '' ? CarbonImmutable::parse($value, 'UTC') : null;
    }

    /**
     * @param  array<mixed>  $data
     */
    protected static function asBool(array $data, string $key, bool $default = false): bool
    {
        return (bool) data_get($data, $key, $default);
    }
}
