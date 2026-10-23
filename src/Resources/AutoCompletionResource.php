<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Enums\AutoCompletionField;
use Oleksyuk\Gleif\Requests\AutoCompletions\AutoCompletions;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class AutoCompletionResource extends BaseResource
{
    public function search(string $query, AutoCompletionField $field = AutoCompletionField::Fulltext): Response
    {
        return $this->connector->send(new AutoCompletions($query, $field));
    }
}
