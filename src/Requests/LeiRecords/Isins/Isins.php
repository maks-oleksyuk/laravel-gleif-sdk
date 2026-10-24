<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiRecords\Isins;

use Oleksyuk\Gleif\Data\Isin;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class Isins extends GleifRequest implements Paginatable
{
    public function __construct(
        private readonly string $id,
        private readonly ?int $pageSize = null,
        private readonly ?int $pageNumber = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/lei-records/'.rawurlencode($this->id).'/isins';
    }

    protected function defaultQuery(): array
    {
        return [
            'page[size]' => $this->pageSize,
            'page[number]' => $this->pageNumber,
        ];
    }

    /** @return Isin[] */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, Isin::fromResponse(...));
    }
}
