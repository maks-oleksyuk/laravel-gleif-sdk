<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\RegistrationAgents;

use Oleksyuk\Gleif\Data\RegistrationAgent;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class AllRegistrationAgents extends GleifRequest
{
    public function __construct(
        private readonly ?string $lei = null,
        private readonly ?string $leiIssuer = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/registration-agents';
    }

    protected function defaultQuery(): array
    {
        return [
            'filter[lei]' => $this->lei,
            'filter[leiIssuer]' => $this->leiIssuer,
        ];
    }

    /**
     * @return RegistrationAgent[]
     */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, RegistrationAgent::fromResponse(...));
    }
}
