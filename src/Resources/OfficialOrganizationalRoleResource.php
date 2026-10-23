<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Requests\OfficialOrganizationalRoles\AllOfficialOrganizationalRoles;
use Oleksyuk\Gleif\Requests\OfficialOrganizationalRoles\OfficialOrganizationalRoleById;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class OfficialOrganizationalRoleResource extends BaseResource
{
    public function all(
        ?string $name = null,
        ?string $countryCode = null,
        ?int $pageSize = null,
        ?int $pageNumber = null,
    ): Response {
        return $this->connector->send(new AllOfficialOrganizationalRoles(
            name: $name,
            countryCode: $countryCode,
            pageSize: $pageSize,
            pageNumber: $pageNumber,
        ));
    }

    public function find(string $id): Response
    {
        return $this->connector->send(new OfficialOrganizationalRoleById($id));
    }
}
