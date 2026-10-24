<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\RegistrationAuthorities;

use Oleksyuk\Gleif\Data\RegistrationAuthority;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class RegistrationAuthorityById extends GleifRequest
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/registration-authorities/'.rawurlencode($this->id);
    }

    public function createDtoFromResponse(Response $response): RegistrationAuthority
    {
        return self::mapOne($response, RegistrationAuthority::fromResponse(...));
    }
}
