<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\Jurisdictions;

use Oleksyuk\Gleif\Data\Jurisdiction;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class JurisdictionById extends GleifRequest
{
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
