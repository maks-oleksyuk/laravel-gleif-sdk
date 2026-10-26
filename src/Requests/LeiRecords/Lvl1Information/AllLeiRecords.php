<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information;

use InvalidArgumentException;
use Oleksyuk\Gleif\Data\LeiRecord;
use Oleksyuk\Gleif\Enums\EntityCategory;
use Oleksyuk\Gleif\Enums\PolicyConformityFlag;
use Oleksyuk\Gleif\Enums\RegistrationStatus;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class AllLeiRecords extends GleifRequest implements Paginatable
{
    /**
     * The LEIs travel in the URL, and GLEIF answers 414 URI Too Long past ~355 of them (see Export).
     */
    private const int MAX_LEIS = 300;

    /**
     * @param  string[]  $lei
     *
     * @throws InvalidArgumentException when there are more than 300 LEIs
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
    ) {
        if (count($this->lei) > self::MAX_LEIS) {
            throw new InvalidArgumentException('GLEIF filters by up to '.self::MAX_LEIS.' LEIs at a time, got '.count($this->lei).'.');
        }
    }

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
