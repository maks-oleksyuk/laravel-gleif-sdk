<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\RegistrationAgents;

use Oleksyuk\Gleif\Data\RegistrationAgent;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class RegistrationAgentById extends GleifRequest
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/registration-agents/'.rawurlencode($this->id);
    }

    public function createDtoFromResponse(Response $response): RegistrationAgent
    {
        return self::mapOne($response, RegistrationAgent::fromResponse(...));
    }
}
