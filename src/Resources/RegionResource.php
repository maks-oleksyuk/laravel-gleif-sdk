<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Requests\Regions\AllRegions;
use Oleksyuk\Gleif\Requests\Regions\RegionById;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class RegionResource extends BaseResource
{
    public function all(?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new AllRegions(
            pageSize: $pageSize,
            pageNumber: $pageNumber,
        ));
    }

    public function find(string $id): Response
    {
        return $this->connector->send(new RegionById($id));
    }
}
