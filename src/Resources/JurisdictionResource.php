<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Requests\Jurisdictions\AllJurisdictions;
use Oleksyuk\Gleif\Requests\Jurisdictions\JurisdictionById;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class JurisdictionResource extends BaseResource
{
    public function all(?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new AllJurisdictions(
            pageSize: $pageSize,
            pageNumber: $pageNumber,
        ));
    }

    public function find(string $id): Response
    {
        return $this->connector->send(new JurisdictionById($id));
    }
}
