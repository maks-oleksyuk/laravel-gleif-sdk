<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Concerns;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Data\CachedResponse;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Helpers\CacheKeyHelper;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Data\RecordedResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;

trait HasGleifCache
{
    use HasCaching;

    /**
     * GLEIF answers 404 for a record that doesn't exist and for an entity without a parent.
     * That answer only changes with the next Golden Copy as well, so it is cached like a 200:
     * the cache plugin skips failed responses, and a cache hit doesn't count against the rate limit.
     */
    public function bootHasGleifCache(PendingRequest $pendingRequest): void
    {
        if ($this->cachingEnabled === false) {
            return;
        }

        $pendingRequest->middleware()->onResponse(function (Response $response): void {
            if ($response->status() !== 404 || $response->isCached()) {
                return;
            }

            $pendingRequest = $response->getPendingRequest();
            $ttl = $this->cacheExpiryInSeconds();

            $this->resolveCacheDriver()->set(
                key: CacheKeyHelper::createHashed($pendingRequest, $this->cacheKey($pendingRequest)),
                cachedResponse: new CachedResponse(RecordedResponse::fromResponse($response), new DateTimeImmutable('+'.$ttl.' seconds'), $ttl),
            );
        });
    }

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
