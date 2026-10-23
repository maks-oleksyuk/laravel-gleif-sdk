<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Carbon\CarbonImmutable;
use Oleksyuk\Gleif\Concerns\ParsesApiResponse;

final readonly class ReportingException
{
    use ParsesApiResponse;

    public function __construct(
        public string $id,
        public string $lei,
        public string $category,
        public string $reason,
        public ?string $reference,
        public ?CarbonImmutable $validFrom,
        public ?CarbonImmutable $validTo,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: self::asString($data, 'id'),
            lei: self::asString($data, 'attributes.lei'),
            category: self::asString($data, 'attributes.category'),
            reason: self::asString($data, 'attributes.reason'),
            reference: self::asString($data, 'attributes.reference') ?: null,
            validFrom: self::asDate($data, 'attributes.validFrom'),
            validTo: self::asDate($data, 'attributes.validTo'),
        );
    }
}
