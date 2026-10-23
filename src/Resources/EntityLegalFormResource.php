<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Requests\EntityLegalForms\AllEntityLegalForms;
use Oleksyuk\Gleif\Requests\EntityLegalForms\EntityLegalFormById;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class EntityLegalFormResource extends BaseResource
{
    public function all(?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new AllEntityLegalForms(
            pageSize: $pageSize,
            pageNumber: $pageNumber,
        ));
    }

    public function find(string $id): Response
    {
        return $this->connector->send(new EntityLegalFormById($id));
    }
}
