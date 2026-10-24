<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\Fields;

use Oleksyuk\Gleif\Data\Field;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class FieldById extends GleifRequest
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/fields/'.rawurlencode($this->id);
    }

    public function createDtoFromResponse(Response $response): Field
    {
        return self::mapOne($response, Field::fromResponse(...));
    }
}
