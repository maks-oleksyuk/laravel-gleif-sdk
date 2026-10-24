<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Oleksyuk\Gleif\Concerns\ParsesApiResponse;

final readonly class Region
{
    use ParsesApiResponse;

    /**
     * @param  array<int, array{language: string, name: string}>  $names
     */
    public function __construct(
        public string $id,
        public ?string $name,
        public array $names,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        /** @var array<int, array{language: string, name: string}> $names */
        $names = self::asArray($data, 'attributes.names');

        return new self(
            id: self::asString($data, 'id'),
            name: self::asNullableString($data, 'attributes.name'),
            names: $names,
        );
    }
}
