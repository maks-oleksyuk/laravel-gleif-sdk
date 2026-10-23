<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Carbon\CarbonImmutable;
use Oleksyuk\Gleif\Concerns\ParsesApiResponse;

final readonly class RelationshipRecord
{
    use ParsesApiResponse;

    /**
     * @param  array{id: string, type: string}|null  $relationshipStartNode
     * @param  array{id: string, type: string}|null  $relationshipEndNode
     * @param  array<int, array{startDate: ?string, endDate: ?string, type: string}>  $relationshipPeriods
     * @param  array<string, mixed>|null  $extension
     */
    public function __construct(
        public string $id,
        public ?CarbonImmutable $validFrom,
        public ?CarbonImmutable $validTo,

        // Relationship properties
        public ?array $relationshipStartNode,
        public ?array $relationshipEndNode,
        public ?string $relationshipType,
        public ?string $relationshipStatus,
        public array $relationshipPeriods,

        // Registration properties
        public ?CarbonImmutable $registrationInitialRegistrationDate,
        public ?CarbonImmutable $registrationLastUpdateDate,
        public ?string $registrationStatus,
        public ?CarbonImmutable $registrationNextRenewalDate,
        public ?string $registrationManagingLou,
        public ?string $registrationCorroborationLevel,
        public ?string $registrationCorroborationDocuments,
        public ?string $registrationCorroborationReference,

        public ?array $extension,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        /** @var array{id: string, type: string}|null $startNode */
        $startNode = self::asArray($data, 'attributes.relationship.startNode') ?: null;

        /** @var array{id: string, type: string}|null $endNode */
        $endNode = self::asArray($data, 'attributes.relationship.endNode') ?: null;

        /** @var array<int, array{startDate: ?string, endDate: ?string, type: string}> $periods */
        $periods = self::asArray($data, 'attributes.relationship.periods');

        $extension = self::asArray($data, 'attributes.extension') ?: null;

        return new self(
            id: self::asString($data, 'id'),
            validFrom: self::asDate($data, 'attributes.validFrom'),
            validTo: self::asDate($data, 'attributes.validTo'),

            // Relationship
            relationshipStartNode: $startNode,
            relationshipEndNode: $endNode,
            relationshipType: self::asString($data, 'attributes.relationship.type') ?: null,
            relationshipStatus: self::asString($data, 'attributes.relationship.status') ?: null,
            relationshipPeriods: $periods,

            // Registration
            registrationInitialRegistrationDate: self::asDate($data, 'attributes.registration.initialRegistrationDate'),
            registrationLastUpdateDate: self::asDate($data, 'attributes.registration.lastUpdateDate'),
            registrationStatus: self::asString($data, 'attributes.registration.status') ?: null,
            registrationNextRenewalDate: self::asDate($data, 'attributes.registration.nextRenewalDate'),
            registrationManagingLou: self::asString($data, 'attributes.registration.managingLou') ?: null,
            registrationCorroborationLevel: self::asString($data, 'attributes.registration.corroborationLevel') ?: null,
            registrationCorroborationDocuments: self::asString($data, 'attributes.registration.corroborationDocuments') ?: null,
            registrationCorroborationReference: self::asString($data, 'attributes.registration.corroborationReference') ?: null,

            extension: $extension,
        );
    }
}
