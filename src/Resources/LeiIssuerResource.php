<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Requests\LeiIssuers\AllLeiIssuers;
use Oleksyuk\Gleif\Requests\LeiIssuers\LeiIssuerById;
use Oleksyuk\Gleif\Requests\LeiIssuers\LeiIssuerJurisdictionById;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class LeiIssuerResource extends BaseResource
{
    public function all(?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new AllLeiIssuers(
            pageSize: $pageSize,
            pageNumber: $pageNumber,
        ));
    }

    public function find(string $id): Response
    {
        return $this->connector->send(new LeiIssuerById($id));
    }

    public function jurisdictions(string $id, ?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new LeiIssuerJurisdictionById($id, $pageSize, $pageNumber));
    }
}
