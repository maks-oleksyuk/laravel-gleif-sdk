<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\RegistrationAuthorities;

use Oleksyuk\Gleif\Concerns\HasGleifCache;
use Oleksyuk\Gleif\Concerns\MapsResponseData;
use Oleksyuk\Gleif\Data\RegistrationAuthority;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class RegistrationAuthorityById extends Request implements Cacheable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/registration-authorities/'.$this->id;
    }

    public function createDtoFromResponse(Response $response): RegistrationAuthority
    {
        return self::mapOne($response, RegistrationAuthority::fromResponse(...));
    }
}
