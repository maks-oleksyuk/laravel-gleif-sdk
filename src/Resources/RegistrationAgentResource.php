<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Requests\RegistrationAgents\AllRegistrationAgents;
use Oleksyuk\Gleif\Requests\RegistrationAgents\RegistrationAgentById;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class RegistrationAgentResource extends BaseResource
{
    public function all(?string $lei = null, ?string $leiIssuer = null): Response
    {
        return $this->connector->send(new AllRegistrationAgents($lei, $leiIssuer));
    }

    public function find(string $id): Response
    {
        return $this->connector->send(new RegistrationAgentById($id));
    }
}
