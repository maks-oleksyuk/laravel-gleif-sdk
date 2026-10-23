<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Requests\VLeiIssuers\AllVLeiIssuers;
use Oleksyuk\Gleif\Requests\VLeiIssuers\VLeiIssuerById;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class VLeiIssuerResource extends BaseResource
{
    public function all(?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new AllVLeiIssuers(
            pageSize: $pageSize,
            pageNumber: $pageNumber,
        ));
    }

    public function find(string $id): Response
    {
        return $this->connector->send(new VLeiIssuerById($id));
    }
}
