<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\EntityLegalForms;

use Oleksyuk\Gleif\Data\EntityLegalForm;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class EntityLegalFormById extends GleifRequest
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/entity-legal-forms/'.rawurlencode($this->id);
    }

    public function createDtoFromResponse(Response $response): EntityLegalForm
    {
        return self::mapOne($response, EntityLegalForm::fromResponse(...));
    }
}
