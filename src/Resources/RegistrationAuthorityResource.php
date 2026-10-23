<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Requests\RegistrationAuthorities\AllRegistrationAuthorities;
use Oleksyuk\Gleif\Requests\RegistrationAuthorities\RegistrationAuthorityById;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class RegistrationAuthorityResource extends BaseResource
{
    public function all(?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new AllRegistrationAuthorities(
            pageSize: $pageSize,
            pageNumber: $pageNumber,
        ));
    }

    public function find(string $id): Response
    {
        return $this->connector->send(new RegistrationAuthorityById($id));
    }
}
