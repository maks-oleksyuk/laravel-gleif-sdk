<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiRecords\LeiIssuer;

use Oleksyuk\Gleif\Data\LeiIssuer as LeiIssuerDto;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class LeiIssuer extends GleifRequest
{
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
