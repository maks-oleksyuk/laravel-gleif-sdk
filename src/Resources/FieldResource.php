<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Requests\Fields\AllFields;
use Oleksyuk\Gleif\Requests\Fields\FieldById;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class FieldResource extends BaseResource
{
    public function all(?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new AllFields(
            pageSize: $pageSize,
            pageNumber: $pageNumber,
        ));
    }

    public function find(string $id): Response
    {
        return $this->connector->send(new FieldById($id));
    }
}
