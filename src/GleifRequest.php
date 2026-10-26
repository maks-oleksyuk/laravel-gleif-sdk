<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use JsonException;
use RuntimeException;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Data\CachedResponse;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Helpers\CacheKeyHelper;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Data\RecordedResponse;
use Saloon\Enums\Method;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use Saloon\Http\Response;

/**
 * A cached GET request to the GLEIF API whose response maps to DTOs from Oleksyuk\Gleif\Data.
 */
abstract class GleifRequest extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    /**
     * How long to cache an answer from a Golden Copy older than the latest publication:
     * the API picks up a new copy hours after it is published, so it is asked again soon.
     */
    private const int STALE_COPY_TTL = 15 * 60;

    /**
     * The cache plugin stores successful responses until the next publication. On top of that:
     *  - a 404 (a record that doesn't exist, an entity without a parent) only changes with the next
     *    Golden Copy as well, so it is cached the same way: the plugin skips failed responses;
     *  - an answer from an outdated copy (meta.goldenCopy.publishDate) is cached for STALE_COPY_TTL only.
     * A cache hit doesn't count against the rate limit.
     */
    public function boot(PendingRequest $pendingRequest): void
    {
        if ($this->cachingEnabled === false) {
            return;
        }

        $pendingRequest->middleware()->onResponse(function (Response $response): void {
            if ($response->isCached()) {
                return;
            }

            $ttl = match (true) {
                $response->status() === 404 => $this->cacheExpiryInSeconds(),
                $response->successful() && $this->isFromStaleCopy($response) => self::STALE_COPY_TTL,
                default => null,
            };

            if ($ttl === null) {
                return;
            }

            $pendingRequest = $response->getPendingRequest();

            $this->resolveCacheDriver()->set(
                key: CacheKeyHelper::createHashed($pendingRequest, $this->cacheKey($pendingRequest)),
                cachedResponse: new CachedResponse(RecordedResponse::fromResponse($response), new DateTimeImmutable('+'.$ttl.' seconds'), $ttl),
            );
        });
    }

    /**
     * Until the next Golden Copy publication: the 00:00, 08:00 and 16:00 UTC copies are published two hours later.
     *
     * @see https://gleif.org/en/lei-data/gleif-golden-copy
     */
    public function cacheExpiryInSeconds(): int
    {
        $now = CarbonImmutable::now('UTC');

        return (int) $now->diffInSeconds($this->latestPublishedCopy($now)->addHours(8 + 2));
    }

    /**
     * The date (meta.goldenCopy.publishDate) of the newest copy whose files are already out.
     */
    private function latestPublishedCopy(CarbonImmutable $now): CarbonImmutable
    {
        $copyDate = $now->subHours(2);

        return $copyDate->startOfDay()->addHours(intdiv($copyDate->hour, 8) * 8);
    }

    /**
     * Only LEI record responses carry the copy's publish date; the rest (reference data, a 404)
     * can't tell, so they are cached until the next publication.
     */
    private function isFromStaleCopy(Response $response): bool
    {
        try {
            $publishDate = $response->json('meta.goldenCopy.publishDate');
        } catch (JsonException) {
            return false;
        }

        return is_string($publishDate) && CarbonImmutable::parse($publishDate)->lt($this->latestPublishedCopy(CarbonImmutable::now('UTC')));
    }

    public function resolveCacheDriver(): Driver
    {
        /** @var string|null $store */
        $store = Config::get('gleif.cache.store');

        return new LaravelCacheDriver(Cache::store($store));
    }

    /**
     * @template T
     *
     * @param  callable(array<mixed>): T  $map
     * @return list<T>
     *
     * @throws RequestException when GLEIF answered with an error
     */
    protected static function mapMany(Response $response, callable $map): array
    {
        $response->throw();

        $data = $response->json('data');

        if (! is_array($data)) {
            return [];
        }

        return array_values(array_map($map, array_filter($data, is_array(...))));
    }

    /**
     * @template T
     *
     * @param  callable(array<mixed>): T  $map
     * @return T
     *
     * @throws RequestException when GLEIF answered with an error (e.g. 404 for a missing parent)
     * @throws RuntimeException when the response has no `data` object
     */
    protected static function mapOne(Response $response, callable $map): mixed
    {
        $response->throw();

        $data = $response->json('data');

        if (! is_array($data)) {
            throw new RuntimeException('Invalid response data');
        }

        return $map($data);
    }
}
