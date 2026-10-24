<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Oleksyuk\Gleif\Concerns\ParsesApiResponse;

final readonly class FuzzyCompletion
{
    use ParsesApiResponse;

    public function __construct(
        public ?string $id,
        public string $value,
        public ?string $lei,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: self::asNullableString($data, 'id'),
            value: self::asString($data, 'attributes.value'),
            lei: self::asNullableString($data, 'relationships.lei-records.data.id'),
        );
    }
}
