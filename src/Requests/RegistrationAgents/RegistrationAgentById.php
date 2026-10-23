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

final class RegistrationAgentById extends Request implements Cacheable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/registration-agents/'.$this->id;
    }

    public function createDtoFromResponse(Response $response): RegistrationAgent
    {
        return self::mapOne($response, RegistrationAgent::fromResponse(...));
    }
}
