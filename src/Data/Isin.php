<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Oleksyuk\Gleif\Concerns\ParsesApiResponse;

final readonly class Isin
{
    use ParsesApiResponse;

    public function __construct(
        public string $id,
        public string $isin,
        public string $lei,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: self::asString($data, 'id'),
            isin: self::asString($data, 'attributes.isin'),
            lei: self::asString($data, 'attributes.lei'),
        );
    }
}
