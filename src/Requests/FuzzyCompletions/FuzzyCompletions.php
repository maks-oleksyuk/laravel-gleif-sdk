<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\FuzzyCompletions;

use Oleksyuk\Gleif\Data\FuzzyCompletion;
use Oleksyuk\Gleif\Enums\FuzzyCompletionField;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class FuzzyCompletions extends GleifRequest
{
    public function __construct(
        private readonly string $search,
        private readonly FuzzyCompletionField $field = FuzzyCompletionField::EntityLegalName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/fuzzycompletions';
    }

    protected function defaultQuery(): array
    {
        return [
            'q' => $this->search,
            'field' => $this->field->value,
        ];
    }

    /** @return FuzzyCompletion[] */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, FuzzyCompletion::fromResponse(...));
    }
}
