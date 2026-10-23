<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Enums\FuzzyCompletionField;
use Oleksyuk\Gleif\Requests\FuzzyCompletions\FuzzyCompletions;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class FuzzyCompletionResource extends BaseResource
{
    public function search(string $query, FuzzyCompletionField $field = FuzzyCompletionField::EntityLegalName): Response
    {
        return $this->connector->send(new FuzzyCompletions($query, $field));
    }
}
