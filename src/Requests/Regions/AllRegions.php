<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\Regions;

use Oleksyuk\Gleif\Data\Region;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class AllRegions extends GleifRequest implements Paginatable
{
    public function __construct(
        private readonly ?int $pageSize = null,
        private readonly ?int $pageNumber = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/regions';
    }

    protected function defaultQuery(): array
    {
        return [
            'page[size]' => $this->pageSize,
            'page[number]' => $this->pageNumber,
        ];
    }

    /**
     * @return Region[]
     */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, Region::fromResponse(...));
    }
}
