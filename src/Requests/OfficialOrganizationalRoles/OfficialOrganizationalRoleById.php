<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\OfficialOrganizationalRoles;

use Oleksyuk\Gleif\Data\OfficialOrganizationalRole;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class OfficialOrganizationalRoleById extends GleifRequest
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/official-organizational-roles/'.rawurlencode($this->id);
    }

    public function createDtoFromResponse(Response $response): OfficialOrganizationalRole
    {
        return self::mapOne($response, OfficialOrganizationalRole::fromResponse(...));
    }
}
