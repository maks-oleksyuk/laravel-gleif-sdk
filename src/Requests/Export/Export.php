<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\Export;

use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;

final class Export extends SoloRequest
{
    protected Method $method = Method::GET;

    /**
     * @param  'csv'|'xlsx'|'json'|'xml'  $format
     * @param  string[]  $lei
     */
    public function __construct(
        private readonly string $format,
        private readonly array $lei = [],
    ) {}

    public function resolveEndpoint(): string
    {
        return 'https://api.gleif.org/export/v1/lei-records.'.rawurlencode($this->format);
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            'filter[lei]' => implode(',', $this->lei),
        ]);
    }
}
