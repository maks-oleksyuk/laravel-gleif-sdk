<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Oleksyuk\Gleif\Concerns\ParsesApiResponse;

final readonly class RegistrationAgent
{
    use ParsesApiResponse;

    /**
     * @param  string[]  $websites
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $lei,
        public ?string $leiIssuer,
        public array $websites,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        /** @var string[] $websites */
        $websites = self::asArray($data, 'attributes.websites');

        return new self(
            id: self::asString($data, 'id'),
            name: self::asString($data, 'attributes.name'),
            lei: self::asNullableString($data, 'attributes.lei'),
            leiIssuer: self::asNullableString($data, 'attributes.leiIssuer'),
            websites: $websites,
        );
    }
}
