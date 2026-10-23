<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information;

use Oleksyuk\Gleif\Concerns\HasGleifCache;
use Oleksyuk\Gleif\Concerns\MapsResponseData;
use Oleksyuk\Gleif\Data\LeiRecord;
use Oleksyuk\Gleif\Enums\EntityCategory;
use Oleksyuk\Gleif\Enums\PolicyConformityFlag;
use Oleksyuk\Gleif\Enums\RegistrationStatus;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class AllLeiRecords extends Request implements Cacheable, Paginatable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

    /**
     * @param  string[]  $lei
     */
    public function __construct(
        private readonly array $lei = [],
        private readonly ?string $bic = null,
        private readonly ?string $isin = null,
        private readonly ?string $owns = null,
        private readonly ?string $ownedBy = null,
        private readonly ?EntityCategory $entityCategory = null,
        private readonly ?string $entityLegalName = null,
        private readonly ?string $entityLegalAddressCountry = null,
        private readonly ?RegistrationStatus $registrationStatus = null,
        private readonly ?PolicyConformityFlag $conformityFlag = null,
        private readonly ?string $fulltext = null,
        private readonly ?string $sort = null,
        private readonly ?int $pageSize = null,
        private readonly ?int $pageNumber = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/lei-records';
    }

    protected function defaultQuery(): array
    {
        return [
            'filter[lei]' => implode(',', $this->lei),
            'filter[bic]' => $this->bic,
            'filter[isin]' => $this->isin,
            'filter[owns]' => $this->owns,
            'filter[ownedBy]' => $this->ownedBy,
            'filter[entity.category]' => $this->entityCategory?->value,
            'filter[entity.legalName]' => $this->entityLegalName,
            'filter[entity.legalAddress.country]' => $this->entityLegalAddressCountry,
            'filter[registration.status]' => $this->registrationStatus?->value,
            'filter[conformityFlag]' => $this->conformityFlag?->value,
            'filter[fulltext]' => $this->fulltext,
            'sort' => $this->sort,
            'page[size]' => $this->pageSize,
            'page[number]' => $this->pageNumber,
        ];
    }

    /**
     * @return LeiRecord[]
     */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, LeiRecord::fromResponse(...));
    }
}
