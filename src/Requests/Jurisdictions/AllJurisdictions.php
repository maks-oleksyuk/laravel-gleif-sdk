<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\Jurisdictions;

use Oleksyuk\Gleif\Data\Jurisdiction;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class AllJurisdictions extends GleifRequest implements Paginatable
{
    public function __construct(
        private readonly ?int $pageSize = null,
        private readonly ?int $pageNumber = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/jurisdictions';
    }

    protected function defaultQuery(): array
    {
        return [
            'page[size]' => $this->pageSize,
            'page[number]' => $this->pageNumber,
        ];
    }

    /**
     * @return Jurisdiction[]
     */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, Jurisdiction::fromResponse(...));
    }
}
