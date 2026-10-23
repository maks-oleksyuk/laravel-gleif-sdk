<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Concerns;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;

trait HasGleifCache
{
    use HasCaching;

    /**
     * @see https://gleif.org/en/lei-data/gleif-golden-copy
     */
    public function cacheExpiryInSeconds(): int
    {
        $now = CarbonImmutable::now('UTC');

        // This ensures that if we hit the API right in the publication window,
        // we don't accidentally cache old data if the publication is slightly delayed.
        $bufferMinutes = 15;

        $publications = [
            $now->setTime(2, $bufferMinutes),
            $now->setTime(10, $bufferMinutes),
            $now->setTime(18, $bufferMinutes),
        ];

        foreach ($publications as $time) {
            if ($now->lt($time)) {
                return (int) $now->diffInSeconds($time);
            }
        }

        return (int) $now->diffInSeconds($now->addDay()->setTime(2, $bufferMinutes));
    }

    public function resolveCacheDriver(): Driver
    {
        /** @var string|null $store */
        $store = config('gleif.cache.store', config('cache.default'));

        return new LaravelCacheDriver(
            Cache::store($store)
        );
    }
}
