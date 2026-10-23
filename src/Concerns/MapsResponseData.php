<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Concerns;

use RuntimeException;
use Saloon\Http\Response;

trait MapsResponseData
{
    /**
     * @template T
     *
     * @param  callable(array<mixed>): T  $map
     * @return list<T>
     */
    protected static function mapMany(Response $response, callable $map): array
    {
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
     * @throws RuntimeException when the response has no `data` object
     */
    protected static function mapOne(Response $response, callable $map): mixed
    {
        $data = $response->json('data');

        if (! is_array($data)) {
            throw new RuntimeException('Invalid response data');
        }

        return $map($data);
    }
}
