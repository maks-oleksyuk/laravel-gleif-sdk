<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\Regions;

use Oleksyuk\Gleif\Data\Region;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class RegionById extends GleifRequest
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/regions/'.rawurlencode($this->id);
    }

    public function createDtoFromResponse(Response $response): Region
    {
        return self::mapOne($response, Region::fromResponse(...));
    }
}
