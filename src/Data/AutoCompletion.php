<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Oleksyuk\Gleif\Concerns\ParsesApiResponse;

final readonly class AutoCompletion
{
    use ParsesApiResponse;

    public function __construct(
        public ?string $id,
        public string $value,
        public ?string $highlighting,
        public ?string $lei,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        $id = self::asString($data, 'id');

        return new self(
            id: $id ?: null,
            value: self::asString($data, 'attributes.value'),
            highlighting: self::asString($data, 'attributes.highlighting') ?: null,
            lei: self::asString($data, 'relationships.lei-records.data.id') ?: null,
        );
    }
}
