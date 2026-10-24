<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\Countries;

use Oleksyuk\Gleif\Data\Country;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class CountryCodeById extends GleifRequest
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/countries/'.rawurlencode($this->id);
    }

    public function createDtoFromResponse(Response $response): Country
    {
        return self::mapOne($response, Country::fromResponse(...));
    }
}
