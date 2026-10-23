<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Oleksyuk\Gleif\Concerns\ParsesApiResponse;

final readonly class Country
{
    use ParsesApiResponse;

    public function __construct(
        public string $id,
        public string $name,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: self::asString($data, 'id'),
            name: self::asString($data, 'attributes.name'),
        );
    }
}
