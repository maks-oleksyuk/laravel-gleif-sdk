<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiRecords\LeiIssuer;

use Oleksyuk\Gleif\Concerns\HasGleifCache;
use Oleksyuk\Gleif\Concerns\MapsResponseData;
use Oleksyuk\Gleif\Data\LeiIssuer as LeiIssuerDto;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class LeiIssuer extends Request implements Cacheable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/lei-records/'.rawurlencode($this->id).'/lei-issuer';
    }

    public function createDtoFromResponse(Response $response): LeiIssuerDto
    {
        return self::mapOne($response, LeiIssuerDto::fromResponse(...));
    }
}
