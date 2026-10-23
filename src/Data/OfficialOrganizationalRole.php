<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Carbon\CarbonImmutable;
use Oleksyuk\Gleif\Concerns\ParsesApiResponse;

final readonly class OfficialOrganizationalRole
{
    use ParsesApiResponse;

    /**
     * @param  array<int, array{
     *     name: string,
     *     language: string,
     *     languageCode: string,
     *     transliteratedName: ?string
     * }> $names
     */
    public function __construct(
        public string $id,
        public string $code,
        public string $country,
        public ?string $jurisdiction,
        public string $countryCode,
        public ?string $subdivisionCode,
        public ?CarbonImmutable $dateCreated,
        public string $status,
        public string $elfCode,
        public array $names,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        /** @var array<int, array{name: string, language: string, languageCode: string, transliteratedName: ?string}> $names */
        $names = self::asArray($data, 'attributes.names');

        return new self(
            id: self::asString($data, 'id'),
            code: self::asString($data, 'attributes.code'),
            country: self::asString($data, 'attributes.country'),
            jurisdiction: self::asString($data, 'attributes.jurisdiction') ?: null,
            countryCode: self::asString($data, 'attributes.countryCode'),
            subdivisionCode: self::asString($data, 'attributes.subdivisionCode') ?: null,
            dateCreated: self::asDate($data, 'attributes.dateCreated'),
            status: self::asString($data, 'attributes.status'),
            elfCode: self::asString($data, 'attributes.elfCode'),
            names: $names,
        );
    }
}
