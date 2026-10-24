<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Oleksyuk\Gleif\Concerns\ParsesApiResponse;

final readonly class Field
{
    use ParsesApiResponse;

    /**
     * @param  array<int, string>|null  $enum
     * @param  string[]  $operators
     * @param  string[]  $contexts
     */
    public function __construct(
        public string $id,
        public string $field,
        public string $label,
        public string $dataType,
        public ?array $enum,
        public ?string $resource,
        public bool $sortable,
        public array $operators,
        public array $contexts,
        public ?string $jsonPath,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        $enum = self::asArray($data, 'attributes.enum') ?: null;

        /** @var string[] $operators */
        $operators = self::asArray($data, 'attributes.operators');

        /** @var string[] $contexts */
        $contexts = self::asArray($data, 'attributes.contexts');

        return new self(
            id: self::asString($data, 'id'),
            field: self::asString($data, 'attributes.field'),
            label: self::asString($data, 'attributes.label'),
            dataType: self::asString($data, 'attributes.data_type'),
            enum: is_array($enum) ? array_values(array_filter($enum, is_string(...))) : null,
            resource: self::asNullableString($data, 'attributes.resource'),
            sortable: self::asBool($data, 'attributes.sortable'),
            operators: $operators,
            contexts: $contexts,
            jsonPath: self::asNullableString($data, 'attributes.jsonPath'),
        );
    }
}
