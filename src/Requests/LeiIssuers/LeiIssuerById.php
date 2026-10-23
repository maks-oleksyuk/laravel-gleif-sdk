<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiIssuers;

use Oleksyuk\Gleif\Concerns\HasGleifCache;
use Oleksyuk\Gleif\Concerns\MapsResponseData;
use Oleksyuk\Gleif\Data\LeiIssuer;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class LeiIssuerById extends Request implements Cacheable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/lei-issuers/'.$this->id;
    }

    public function createDtoFromResponse(Response $response): LeiIssuer
    {
        return self::mapOne($response, LeiIssuer::fromResponse(...));
    }
}
