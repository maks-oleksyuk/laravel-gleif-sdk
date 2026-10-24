<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiIssuers;

use Oleksyuk\Gleif\Data\LeiIssuerJurisdiction;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class LeiIssuerJurisdictionById extends GleifRequest implements Paginatable
{
    public function __construct(
        private readonly string $id,
        private readonly ?int $pageSize = null,
        private readonly ?int $pageNumber = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/lei-issuers/'.rawurlencode($this->id).'/jurisdictions';
    }

    protected function defaultQuery(): array
    {
        return [
            'page[size]' => $this->pageSize,
            'page[number]' => $this->pageNumber,
        ];
    }

    /**
     * @return LeiIssuerJurisdiction[]
     */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, LeiIssuerJurisdiction::fromResponse(...));
    }
}
