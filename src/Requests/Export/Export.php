<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\Export;

use InvalidArgumentException;
use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;

final class Export extends SoloRequest
{
    private const int MAX_LEIS = 5000;

    protected Method $method = Method::GET;

    /** @var list<string> */
    private readonly array $lei;

    /**
     * @param  'csv'|'xlsx'|'json'|'xml'  $format
     * @param  string[]  $lei
     *
     * @throws InvalidArgumentException when there are no LEIs or more than 5,000
     */
    public function __construct(
        private readonly string $format,
        array $lei,
    ) {
        $this->lei = array_values(array_unique($lei));

        // Without a LEI filter GLEIF exports an arbitrary first page of records instead of failing.
        if ($this->lei === [] || count($this->lei) > self::MAX_LEIS) {
            throw new InvalidArgumentException('GLEIF exports 1 to '.self::MAX_LEIS.' LEIs at a time, got '.count($this->lei).'.');
        }
    }

    public function resolveEndpoint(): string
    {
        return 'https://api.gleif.org/export/v1/lei-records.'.rawurlencode($this->format);
    }

    protected function defaultQuery(): array
    {
        return [
            'filter[lei]' => implode(',', $this->lei),
            // GLEIF exports 15 records unless told otherwise, so the page fits every requested LEI.
            'page[size]' => count($this->lei),
        ];
    }
}
