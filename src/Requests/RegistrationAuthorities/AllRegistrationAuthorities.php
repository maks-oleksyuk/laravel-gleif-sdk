<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\RegistrationAuthorities;

use Oleksyuk\Gleif\Data\RegistrationAuthority;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

final class AllRegistrationAuthorities extends GleifRequest implements Paginatable
{
    public function __construct(
        private readonly ?int $pageSize = null,
        private readonly ?int $pageNumber = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/registration-authorities';
    }

    protected function defaultQuery(): array
    {
        return [
            'page[size]' => $this->pageSize,
            'page[number]' => $this->pageNumber,
        ];
    }

    /**
     * @return RegistrationAuthority[]
     */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, RegistrationAuthority::fromResponse(...));
    }
}
