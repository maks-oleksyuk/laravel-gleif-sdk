<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Oleksyuk\Gleif\Concerns\ParsesApiResponse;

final readonly class RegistrationAuthority
{
    use ParsesApiResponse;

    /**
     * @param  array<int, array{
     *     country: string,
     *     countryCode: string,
     *     jurisdiction: string
     * }>  $jurisdictions
     */
    public function __construct(
        public string $id,
        public string $code,
        public ?string $internationalName,
        public ?string $localName,
        public ?string $internationalOrganizationName,
        public ?string $localOrganizationName,
        public ?string $website,
        public array $jurisdictions,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        /** @var array<int, array{country: string, countryCode: string, jurisdiction: string}> $jurisdictions */
        $jurisdictions = self::asArray($data, 'attributes.jurisdictions');

        return new self(
            id: self::asString($data, 'id'),
            code: self::asString($data, 'attributes.code'),
            internationalName: self::asNullableString($data, 'attributes.internationalName'),
            localName: self::asNullableString($data, 'attributes.localName'),
            internationalOrganizationName: self::asNullableString($data, 'attributes.internationalOrganizationName'),
            localOrganizationName: self::asNullableString($data, 'attributes.localOrganizationName'),
            website: self::asNullableString($data, 'attributes.website'),
            jurisdictions: $jurisdictions,
        );
    }
}
