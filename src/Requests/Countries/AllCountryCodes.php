<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\Countries;

use Oleksyuk\Gleif\Data\Country;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class AllCountryCodes extends GleifRequest implements Paginatable
{
    public function __construct(
        private readonly ?int $pageSize = null,
        private readonly ?int $pageNumber = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/countries';
    }

    protected function defaultQuery(): array
    {
        return [
            'page[size]' => $this->pageSize,
            'page[number]' => $this->pageNumber,
        ];
    }

    /** @return Country[] */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, Country::fromResponse(...));
    }
}
