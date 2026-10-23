<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\Jurisdictions;

use Oleksyuk\Gleif\Concerns\HasGleifCache;
use Oleksyuk\Gleif\Concerns\MapsResponseData;
use Oleksyuk\Gleif\Data\Jurisdiction;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class AllJurisdictions extends Request implements Cacheable, Paginatable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

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
        return array_filter([
            'page[size]' => $this->pageSize,
            'page[number]' => $this->pageNumber,
        ]);
    }

    /**
     * @return Jurisdiction[]
     */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, Jurisdiction::fromResponse(...));
    }
}
