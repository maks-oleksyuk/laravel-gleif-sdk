<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\Fields;

use Oleksyuk\Gleif\Data\Field;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class AllFields extends GleifRequest implements Paginatable
{
    public function __construct(
        private readonly ?int $pageSize = null,
        private readonly ?int $pageNumber = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/fields';
    }

    protected function defaultQuery(): array
    {
        return [
            'page[size]' => $this->pageSize,
            'page[number]' => $this->pageNumber,
        ];
    }

    /** @return Field[] */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, Field::fromResponse(...));
    }
}
