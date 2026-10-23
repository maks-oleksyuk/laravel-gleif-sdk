<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\Jurisdictions;

use Oleksyuk\Gleif\Concerns\HasGleifCache;
use Oleksyuk\Gleif\Concerns\MapsResponseData;
use Oleksyuk\Gleif\Data\Jurisdiction;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class JurisdictionById extends Request implements Cacheable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/jurisdictions/'.rawurlencode($this->id);
    }

    public function createDtoFromResponse(Response $response): Jurisdiction
    {
        return self::mapOne($response, Jurisdiction::fromResponse(...));
    }
}
