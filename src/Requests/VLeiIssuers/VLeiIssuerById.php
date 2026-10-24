<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\VLeiIssuers;

use Oleksyuk\Gleif\Data\VLeiIssuer;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class VLeiIssuerById extends GleifRequest
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/vlei-issuers/'.rawurlencode($this->id);
    }

    public function createDtoFromResponse(Response $response): VLeiIssuer
    {
        return self::mapOne($response, VLeiIssuer::fromResponse(...));
    }
}
