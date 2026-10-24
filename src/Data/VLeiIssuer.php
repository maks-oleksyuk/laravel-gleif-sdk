<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Carbon\CarbonImmutable;
use Oleksyuk\Gleif\Concerns\ParsesApiResponse;

final readonly class VLeiIssuer
{
    use ParsesApiResponse;

    public function __construct(
        public string $id,
        public string $lei,
        public string $name,
        public ?string $marketingName,
        public string $website,
        public ?CarbonImmutable $qualificationDate,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: self::asString($data, 'id'),
            lei: self::asString($data, 'attributes.lei'),
            name: self::asString($data, 'attributes.name'),
            marketingName: self::asNullableString($data, 'attributes.marketingName'),
            website: self::asString($data, 'attributes.website'),
            qualificationDate: self::asDate($data, 'attributes.qualificationDate'),
        );
    }
}
