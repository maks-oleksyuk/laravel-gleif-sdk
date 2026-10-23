<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\AutoCompletions;

use Oleksyuk\Gleif\Concerns\HasGleifCache;
use Oleksyuk\Gleif\Concerns\MapsResponseData;
use Oleksyuk\Gleif\Data\AutoCompletion;
use Oleksyuk\Gleif\Enums\AutoCompletionField;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class AutoCompletions extends Request implements Cacheable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $search,
        private readonly AutoCompletionField $field = AutoCompletionField::Fulltext,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/autocompletions';
    }

    protected function defaultQuery(): array
    {
        return [
            'q' => $this->search,
            'field' => $this->field->value,
        ];
    }

    /** @return AutoCompletion[] */
    public function createDtoFromResponse(Response $response): array
    {
        return self::mapMany($response, AutoCompletion::fromResponse(...));
    }
}
