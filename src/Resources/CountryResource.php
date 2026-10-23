<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Requests\Countries\AllCountryCodes;
use Oleksyuk\Gleif\Requests\Countries\CountryCodeById;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class CountryResource extends BaseResource
{
    public function all(?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new AllCountryCodes(
            pageSize: $pageSize,
            pageNumber: $pageNumber,
        ));
    }

    public function find(string $id): Response
    {
        return $this->connector->send(new CountryCodeById($id));
    }
}
