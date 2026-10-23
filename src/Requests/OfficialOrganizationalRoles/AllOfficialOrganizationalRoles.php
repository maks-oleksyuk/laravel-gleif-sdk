<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\OfficialOrganizationalRoles;

use Oleksyuk\Gleif\Concerns\HasGleifCache;
use Oleksyuk\Gleif\Concerns\MapsResponseData;
use Oleksyuk\Gleif\Data\OfficialOrganizationalRole;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class AllOfficialOrganizationalRoles extends Request implements Cacheable, Paginatable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

    public function __construct(
        private readonly ?string $name = null,
        private readonly ?string $countryCode = null,
        private readonly ?int $pageSize = null,
        private readonly ?int $pageNumber = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/official-organizational-roles';
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            'filter[name]' => $this->name,
            'filter[countryCode]' => $this->countryCode,
            'page[size]' => $this->pageSize,
            'page[number]' => $this->pageNumber,
        ]);
    }

    /**
     * @return OfficialOrganizationalRole[]
     */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, OfficialOrganizationalRole::fromResponse(...));
    }
}
