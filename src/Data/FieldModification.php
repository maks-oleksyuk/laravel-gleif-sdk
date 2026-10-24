<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Carbon\CarbonImmutable;
use Oleksyuk\Gleif\Concerns\ParsesApiResponse;
use Oleksyuk\Gleif\Enums\ModificationType;
use Oleksyuk\Gleif\Enums\RecordType;

final readonly class FieldModification
{
    use ParsesApiResponse;

    /**
     * @param  array<string, mixed>|null  $context
     */
    public function __construct(
        public string $id,
        public string $lei,
        public ?RecordType $recordType,
        public ?ModificationType $modificationType,
        public string $field,
        public ?CarbonImmutable $date,
        public mixed $valueOld,
        public mixed $valueNew,
        public ?array $context,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        $context = self::asArray($data, 'attributes.context') ?: null;

        return new self(
            id: self::asString($data, 'id'),
            lei: self::asString($data, 'attributes.lei'),
            recordType: RecordType::tryFrom(self::asString($data, 'attributes.recordType')),
            modificationType: ModificationType::tryFrom(self::asString($data, 'attributes.modificationType')),
            field: self::asString($data, 'attributes.field'),
            date: self::asDate($data, 'attributes.date'),
            valueOld: data_get($data, 'attributes.valueOld'),
            valueNew: data_get($data, 'attributes.valueNew'),
            context: $context,
        );
    }
}
