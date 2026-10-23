<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiRecords\FieldModifications;

use Oleksyuk\Gleif\Concerns\HasGleifCache;
use Oleksyuk\Gleif\Concerns\MapsResponseData;
use Oleksyuk\Gleif\Data\FieldModification;
use Oleksyuk\Gleif\Enums\ModificationType;
use Oleksyuk\Gleif\Enums\RecordType;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class FieldModifications extends Request implements Cacheable, Paginatable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
        private readonly ?ModificationType $modificationType = null,
        private readonly ?RecordType $recordType = null,
        private readonly ?string $field = null,
        private readonly ?string $date = null,
        private readonly ?int $pageSize = null,
        private readonly ?int $pageNumber = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/lei-records/'.rawurlencode($this->id).'/field-modifications';
    }

    protected function defaultQuery(): array
    {
        return [
            'filter[modificationType]' => $this->modificationType?->value,
            'filter[recordType]' => $this->recordType?->value,
            'filter[field]' => $this->field,
            'filter[date]' => $this->date,
            'page[size]' => $this->pageSize,
            'page[number]' => $this->pageNumber,
        ];
    }

    /**
     * @return FieldModification[]
     */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, FieldModification::fromResponse(...));
    }
}
