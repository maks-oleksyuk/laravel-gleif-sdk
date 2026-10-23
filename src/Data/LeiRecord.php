<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Data;

use Carbon\CarbonImmutable;
use Oleksyuk\Gleif\Concerns\ParsesApiResponse;
use Oleksyuk\Gleif\Enums\EntityCategory;
use Oleksyuk\Gleif\Enums\PolicyConformityFlag;
use Oleksyuk\Gleif\Enums\RegistrationStatus;

final readonly class LeiRecord
{
    use ParsesApiResponse;

    /**
     * @param  array{name: string, language: ?string}|null  $entityLegalName
     * @param  array<int, array{name: string, language: ?string, type: string}>  $entityOtherNames
     * @param  array<int, array{name: string, language: ?string, type: string}>  $entityTransliteratedOtherNames
     * @param  array{language: ?string, addressLines: string[], addressNumber: ?string, addressNumberWithinBuilding: ?string, mailRouting: ?string, city: string, region: ?string, country: string, postalCode: ?string}|null  $entityLegalAddress
     * @param  array{language: ?string, addressLines: string[], addressNumber: ?string, addressNumberWithinBuilding: ?string, mailRouting: ?string, city: string, region: ?string, country: string, postalCode: ?string}|null  $entityHeadquartersAddress
     * @param  array{id: string, other: ?string}|null  $entityRegisteredAt
     * @param  array{id: string, other: ?string}|null  $entityLegalForm
     * @param  array{lei: ?string, name: ?string}|null  $entityAssociatedEntity
     * @param  array{date: ?string, reason: ?string}|null  $entityExpiration
     * @param  array{lei: ?string, name: ?string}|null  $entitySuccessorEntity
     * @param  array<int, array{lei: string}>  $entitySuccessorEntities
     * @param  array<int, array{language: ?string, addressLines: string[], city: string, region: ?string, country: string, postalCode: ?string}>  $entityOtherAddresses
     * @param  array<int, mixed>  $entityEventGroups
     * @param  array{id: string, other: ?string}|null  $registrationValidatedAt
     * @param  array<int, mixed>  $registrationOtherValidationAuthorities
     * @param  string[]|null  $bic
     * @param  string[]|null  $mic
     * @param  string[]|null  $spglobal
     */
    public function __construct(
        public string $id,
        public string $lei,
        // Entity properties
        public ?array $entityLegalName,
        public array $entityOtherNames,
        public array $entityTransliteratedOtherNames,
        public ?array $entityLegalAddress,
        public ?array $entityHeadquartersAddress,
        public ?array $entityRegisteredAt,
        public ?string $entityRegisteredAs,
        public ?string $entityJurisdiction,
        public ?EntityCategory $entityCategory,
        public ?array $entityLegalForm,
        public ?array $entityAssociatedEntity,
        public ?string $entityStatus,
        public ?array $entityExpiration,
        public ?array $entitySuccessorEntity,
        public array $entitySuccessorEntities,
        public ?CarbonImmutable $entityCreationDate,
        public ?string $entitySubCategory,
        public array $entityOtherAddresses,
        public array $entityEventGroups,
        // Registration properties
        public ?CarbonImmutable $registrationInitialRegistrationDate,
        public ?CarbonImmutable $registrationLastUpdateDate,
        public ?RegistrationStatus $registrationStatus,
        public ?CarbonImmutable $registrationNextRenewalDate,
        public ?string $registrationManagingLou,
        public ?string $registrationCorroborationLevel,
        public ?array $registrationValidatedAt,
        public ?string $registrationValidatedAs,
        public array $registrationOtherValidationAuthorities,
        // Other top-level attributes
        public ?array $bic,
        public ?array $mic,
        public ?string $ocid,
        public ?array $spglobal,
        public ?PolicyConformityFlag $conformityFlag,
    ) {}

    /**
     * @param  array<mixed>  $data
     */
    public static function fromResponse(array $data): self
    {
        /** @var array{name: string, language: ?string}|null $legalName */
        $legalName = self::asArray($data, 'attributes.entity.legalName') ?: null;

        /** @var array{language: ?string, addressLines: string[], addressNumber: ?string, addressNumberWithinBuilding: ?string, mailRouting: ?string, city: string, region: ?string, country: string, postalCode: ?string}|null $legalAddress */
        $legalAddress = self::asArray($data, 'attributes.entity.legalAddress') ?: null;

        /** @var array{language: ?string, addressLines: string[], addressNumber: ?string, addressNumberWithinBuilding: ?string, mailRouting: ?string, city: string, region: ?string, country: string, postalCode: ?string}|null $hqAddress */
        $hqAddress = self::asArray($data, 'attributes.entity.headquartersAddress') ?: null;

        /** @var array{id: string, other: ?string}|null $registeredAt */
        $registeredAt = self::asArray($data, 'attributes.entity.registeredAt') ?: null;

        /** @var array{id: string, other: ?string}|null $legalForm */
        $legalForm = self::asArray($data, 'attributes.entity.legalForm') ?: null;

        /** @var array{lei: ?string, name: ?string}|null $associatedEntity */
        $associatedEntity = self::asArray($data, 'attributes.entity.associatedEntity') ?: null;

        /** @var array{date: ?string, reason: ?string}|null $expiration */
        $expiration = self::asArray($data, 'attributes.entity.expiration') ?: null;

        /** @var array{lei: ?string, name: ?string}|null $successorEntity */
        $successorEntity = self::asArray($data, 'attributes.entity.successorEntity') ?: null;

        /** @var array{id: string, other: ?string}|null $validatedAt */
        $validatedAt = self::asArray($data, 'attributes.registration.validatedAt') ?: null;

        $spglobal = self::asArray($data, 'attributes.spglobal') ?: null;

        /** @var array<int, array{name: string, language: ?string, type: string}> $otherNames */
        $otherNames = self::asArray($data, 'attributes.entity.otherNames');

        /** @var array<int, array{name: string, language: ?string, type: string}> $transliteratedOtherNames */
        $transliteratedOtherNames = self::asArray($data, 'attributes.entity.transliteratedOtherNames');

        /** @var array<int, array{lei: string}> $successorEntities */
        $successorEntities = self::asArray($data, 'attributes.entity.successorEntities');

        /** @var array<int, array{language: ?string, addressLines: string[], city: string, region: ?string, country: string, postalCode: ?string}> $otherAddresses */
        $otherAddresses = self::asArray($data, 'attributes.entity.otherAddresses');

        /** @var array<int, mixed> $eventGroups */
        $eventGroups = self::asArray($data, 'attributes.entity.eventGroups');

        /** @var array<int, mixed> $otherValidationAuthorities */
        $otherValidationAuthorities = self::asArray($data, 'attributes.registration.otherValidationAuthorities');

        $bic = self::asArray($data, 'attributes.bic') ?: null;
        $mic = self::asArray($data, 'attributes.mic') ?: null;

        return new self(
            id: self::asString($data, 'id'),
            lei: self::asString($data, 'attributes.lei'),
            // Entity
            entityLegalName: $legalName,
            entityOtherNames: $otherNames,
            entityTransliteratedOtherNames: $transliteratedOtherNames,
            entityLegalAddress: $legalAddress,
            entityHeadquartersAddress: $hqAddress,
            entityRegisteredAt: $registeredAt,
            entityRegisteredAs: self::asString($data, 'attributes.entity.registeredAs') ?: null,
            entityJurisdiction: self::asString($data, 'attributes.entity.jurisdiction') ?: null,
            entityCategory: EntityCategory::tryFrom(self::asString($data, 'attributes.entity.category')),
            entityLegalForm: $legalForm,
            entityAssociatedEntity: $associatedEntity,
            entityStatus: self::asString($data, 'attributes.entity.status') ?: null,
            entityExpiration: $expiration,
            entitySuccessorEntity: $successorEntity,
            entitySuccessorEntities: $successorEntities,
            entityCreationDate: self::asDate($data, 'attributes.entity.creationDate'),
            entitySubCategory: self::asString($data, 'attributes.entity.subCategory') ?: null,
            entityOtherAddresses: $otherAddresses,
            entityEventGroups: $eventGroups,
            // Registration
            registrationInitialRegistrationDate: self::asDate($data, 'attributes.registration.initialRegistrationDate'),
            registrationLastUpdateDate: self::asDate($data, 'attributes.registration.lastUpdateDate'),
            registrationStatus: RegistrationStatus::tryFrom(self::asString($data, 'attributes.registration.status')),
            registrationNextRenewalDate: self::asDate($data, 'attributes.registration.nextRenewalDate'),
            registrationManagingLou: self::asString($data, 'attributes.registration.managingLou') ?: null,
            registrationCorroborationLevel: self::asString($data, 'attributes.registration.corroborationLevel') ?: null,
            registrationValidatedAt: $validatedAt,
            registrationValidatedAs: self::asString($data, 'attributes.registration.validatedAs') ?: null,
            registrationOtherValidationAuthorities: $otherValidationAuthorities,
            // Other
            bic: is_array($bic) ? array_values(array_filter($bic, is_string(...))) : null,
            mic: is_array($mic) ? array_values(array_filter($mic, is_string(...))) : null,
            ocid: self::asString($data, 'attributes.ocid') ?: null,
            spglobal: is_array($spglobal) ? array_values(array_filter($spglobal, is_string(...))) : null,
            conformityFlag: PolicyConformityFlag::tryFrom(self::asString($data, 'attributes.conformityFlag')),
        );
    }
}
