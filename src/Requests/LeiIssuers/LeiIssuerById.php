<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiIssuers;

use Oleksyuk\Gleif\Data\LeiIssuer;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class LeiIssuerById extends GleifRequest
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/lei-issuers/'.rawurlencode($this->id);
    }

    public function createDtoFromResponse(Response $response): LeiIssuer
    {
        return self::mapOne($response, LeiIssuer::fromResponse(...));
    }
}
