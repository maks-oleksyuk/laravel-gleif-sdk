<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Children;

use Oleksyuk\Gleif\Concerns\HasGleifCache;
use Oleksyuk\Gleif\Concerns\MapsResponseData;
use Oleksyuk\Gleif\Data\RelationshipRecord;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class DirectChildRelationship extends Request implements Cacheable, Paginatable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
        private readonly ?int $pageSize = null,
        private readonly ?int $pageNumber = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/lei-records/'.rawurlencode($this->id).'/direct-child-relationships';
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            'page[size]' => $this->pageSize,
            'page[number]' => $this->pageNumber,
        ]);
    }

    /** @return RelationshipRecord[] */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, RelationshipRecord::fromResponse(...));
    }
}
