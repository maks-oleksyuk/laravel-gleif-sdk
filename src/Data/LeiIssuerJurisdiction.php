<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Carbon\CarbonImmutable;
use Oleksyuk\Gleif\Concerns\ParsesApiResponse;

final readonly class LeiIssuerJurisdiction
{
    use ParsesApiResponse;

    public function __construct(
        public string $id,
        public string $countryCode,
        public string $accreditedAs,
        public ?CarbonImmutable $startDate,
        public ?CarbonImmutable $endDate,
        public bool $isAccreditedForFunds,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: self::asString($data, 'id'),
            countryCode: self::asString($data, 'attributes.countryCode'),
            accreditedAs: self::asString($data, 'attributes.accreditedAs'),
            startDate: self::asDate($data, 'attributes.startDate'),
            endDate: self::asDate($data, 'attributes.endDate'),
            isAccreditedForFunds: self::asBool($data, 'attributes.isAccreditedForFunds'),
        );
    }
}
