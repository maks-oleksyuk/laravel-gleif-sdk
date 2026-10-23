<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\RegistrationAgents;

use Oleksyuk\Gleif\Concerns\HasGleifCache;
use Oleksyuk\Gleif\Concerns\MapsResponseData;
use Oleksyuk\Gleif\Data\RegistrationAgent;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class AllRegistrationAgents extends Request implements Cacheable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

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
