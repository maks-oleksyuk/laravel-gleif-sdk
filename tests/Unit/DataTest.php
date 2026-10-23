<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Oleksyuk\Gleif\Data\AutoCompletion;
use Oleksyuk\Gleif\Data\Country;
use Oleksyuk\Gleif\Data\EntityLegalForm;
use Oleksyuk\Gleif\Data\Field;
use Oleksyuk\Gleif\Data\FieldModification;
use Oleksyuk\Gleif\Data\FuzzyCompletion;
use Oleksyuk\Gleif\Data\Isin;
use Oleksyuk\Gleif\Data\Jurisdiction;
use Oleksyuk\Gleif\Data\LeiIssuer;
use Oleksyuk\Gleif\Data\LeiIssuerJurisdiction;
use Oleksyuk\Gleif\Data\LeiRecord;
use Oleksyuk\Gleif\Data\OfficialOrganizationalRole;
use Oleksyuk\Gleif\Data\Region;
use Oleksyuk\Gleif\Data\RegistrationAgent;
use Oleksyuk\Gleif\Data\RegistrationAuthority;
use Oleksyuk\Gleif\Data\RelationshipRecord;
use Oleksyuk\Gleif\Data\ReportingException;
use Oleksyuk\Gleif\Data\VLeiIssuer;
use Oleksyuk\Gleif\Enums\EntityCategory;
use Oleksyuk\Gleif\Enums\ModificationType;
use Oleksyuk\Gleif\Enums\PolicyConformityFlag;
use Oleksyuk\Gleif\Enums\RecordType;
use Oleksyuk\Gleif\Enums\RegistrationStatus;

test('DTO maps a real API resource object', function (Closure $map, string $fixture, array $expected): void {
    expect($map(gleifFixture($fixture)))->toHaveProperties($expected);
})->with([
    'AutoCompletion' => [AutoCompletion::fromResponse(...), 'autocompletion', [
        'id' => null,
        'value' => 'GLEIF Americas A NJ Nonprofit Corporation',
        'highlighting' => '<b>GLEIF</b> Americas A NJ Nonprofit Corporation',
        'lei' => '254900OPPU84GM83MG36',
    ]],
    'Country' => [Country::fromResponse(...), 'country', ['id' => 'DE', 'name' => 'Germany']],
    'EntityLegalForm' => [EntityLegalForm::fromResponse(...), 'entity-legal-form', [
        'id' => '6QQB',
        'code' => '6QQB',
        'country' => 'Germany',
        'jurisdiction' => null,
        'countryCode' => 'DE',
        'subdivisionCode' => null,
        'dateCreated' => CarbonImmutable::parse('2017-11-30', 'UTC'),
        'status' => 'ACTV',
        'names' => [['localName' => 'Aktiengesellschaft', 'language' => 'German', 'languageCode' => 'de', 'transliteratedName' => 'Aktiengesellschaft']],
    ]],
    'Field' => [Field::fromResponse(...), 'field', [
        'id' => 'LEIREC_BRANCH_ENTITY_STATUS',
        'field' => 'branches.entityStatus',
        'label' => 'Branch Entity Status',
        'dataType' => 'ENUM',
        'enum' => ['ACTIVE', 'INACTIVE', 'NULL'],
        'resource' => null,
        'sortable' => true,
        'operators' => ['MATCH', 'NOT_MATCH', 'IN'],
        'contexts' => ['FILTER_LEIREC_BRANCHES'],
        'jsonPath' => null,
    ]],
    'FieldModification' => [FieldModification::fromResponse(...), 'field-modification', [
        'id' => '59a788be-51ba-4fc7-b5fc-4b9c24169460',
        'lei' => '7LTWFZYICNSX8D621K86',
        'recordType' => RecordType::Lei,
        'modificationType' => ModificationType::Update,
        'field' => '/lei:LEIData/lei:LEIRecords/lei:LEIRecord/lei:Registration/lei:NextRenewalDate',
        'date' => CarbonImmutable::parse('2026-04-07T16:00:00Z'),
        'valueOld' => '2026-06-02T08:47:59+02:00',
        'valueNew' => '2027-06-02T08:47:59+02:00',
        'context' => null,
    ]],
    'FuzzyCompletion' => [FuzzyCompletion::fromResponse(...), 'fuzzycompletion', ['id' => null, 'value' => 'GLEFF AS', 'lei' => '5967007LIEEXZXJL2833']],
    'Isin' => [Isin::fromResponse(...), 'isin', ['id' => 'eb3d7d08-1fbb-4bef-98ef-2a9b021925ed', 'isin' => 'DE000DH4HMJ0', 'lei' => '7LTWFZYICNSX8D621K86']],
    'Jurisdiction' => [Jurisdiction::fromResponse(...), 'jurisdiction', ['id' => 'DE', 'name' => 'Germany']],
    'LeiIssuer' => [LeiIssuer::fromResponse(...), 'lei-issuer', [
        'id' => '5299000J2N45DDNE4Y28',
        'lei' => '5299000J2N45DDNE4Y28',
        'name' => 'WM Datenservice',
        'marketingName' => 'WM Datenservice',
        'website' => 'https://www.wm-leiportal.org/',
        'accreditationDate' => CarbonImmutable::parse('2017-04-13T00:00:00+00:00'),
    ]],
    'LeiIssuerJurisdiction' => [LeiIssuerJurisdiction::fromResponse(...), 'lei-issuer-jurisdiction', [
        'id' => '105299-620f89d76d80f2.70830467',
        'countryCode' => 'AE',
        'accreditedAs' => 'FULLY\/PARTIALLY CORROBORATED', // sic: GLEIF double-escapes the slash
        'startDate' => CarbonImmutable::parse('2017-04-26T00:00:00+00:00'),
        'endDate' => null,
        'isAccreditedForFunds' => true,
    ]],
    'LeiRecord' => [LeiRecord::fromResponse(...), 'lei-record', [
        'id' => '7LTWFZYICNSX8D621K86',
        'lei' => '7LTWFZYICNSX8D621K86',
        'entityLegalName' => ['name' => 'DEUTSCHE BANK AKTIENGESELLSCHAFT', 'language' => 'de'],
        'entityOtherNames' => [],
        'entityLegalAddress' => [
            'language' => 'de',
            'addressLines' => ['Taunusanlage 12'],
            'addressNumber' => null,
            'addressNumberWithinBuilding' => null,
            'mailRouting' => null,
            'city' => 'Frankfurt am Main',
            'region' => 'DE-HE',
            'country' => 'DE',
            'postalCode' => '60325',
        ],
        'entityRegisteredAt' => ['id' => 'RA000242', 'other' => null],
        'entityRegisteredAs' => 'HRB 30000',
        'entityJurisdiction' => 'DE',
        'entityCategory' => EntityCategory::General,
        'entityLegalForm' => ['id' => '6QQB', 'other' => null],
        'entityStatus' => 'ACTIVE',
        'entityCreationDate' => CarbonImmutable::parse('1952-11-10T23:00:00Z'),
        'entitySubCategory' => null,
        'registrationInitialRegistrationDate' => CarbonImmutable::parse('2012-06-06T15:51:15Z'),
        'registrationStatus' => RegistrationStatus::Issued,
        'registrationManagingLou' => '5299000J2N45DDNE4Y28',
        'registrationCorroborationLevel' => 'FULLY_CORROBORATED',
        'registrationValidatedAs' => 'HRB 30000',
        'bic' => ['DBSCESMMXXX', 'DEUTAEAAXXX', 'DEUTATWWXXX'],
        'mic' => ['DBOX'],
        'ocid' => 'de/M1201_HRB30000',
        'spglobal' => ['410467'],
        'conformityFlag' => PolicyConformityFlag::Conforming,
    ]],
    'OfficialOrganizationalRole' => [OfficialOrganizationalRole::fromResponse(...), 'official-organizational-role', [
        'id' => '01D0O4',
        'code' => '01D0O4',
        'country' => 'Qatar',
        'countryCode' => 'QA',
        'dateCreated' => CarbonImmutable::parse('2023-06-28', 'UTC'),
        'status' => 'ACTV',
        'elfCode' => 'RN1W',
        'names' => [['name' => 'موظف', 'language' => 'Arabic', 'languageCode' => 'ar', 'transliteratedName' => 'muwadaf']],
    ]],
    'Region' => [Region::fromResponse(...), 'region', ['id' => 'AD-02', 'name' => null, 'names' => [['language' => 'ca', 'name' => 'Canillo']]]],
    'RegistrationAgent' => [RegistrationAgent::fromResponse(...), 'registration-agent', [
        'id' => '5d10d4dc929ab6.72309473',
        'name' => 'CDS Securities Management Solutions',
        'lei' => null,
        'leiIssuer' => 'EVK05KS7XY1DEII3R011',
        'websites' => ['https://www.cds.ca/cds-products/cds-clearing/legal-entity-identifier-lei'],
    ]],
    'RegistrationAuthority' => [RegistrationAuthority::fromResponse(...), 'registration-authority', [
        'id' => 'RA000242',
        'code' => 'RA000242',
        'internationalName' => 'Commercial Register',
        'localName' => 'Handelsregister',
        'internationalOrganizationName' => 'Local Court Frankfurt am Main',
        'localOrganizationName' => 'Amtsgericht Frankfurt am Main',
        'website' => 'www.handelsregister.de',
        'jurisdictions' => [['country' => 'Germany', 'countryCode' => 'DE', 'jurisdiction' => 'Frankfurt am Main']],
    ]],
    'RelationshipRecord' => [RelationshipRecord::fromResponse(...), 'relationship-record', [
        'validFrom' => CarbonImmutable::parse('2024-05-15T08:00:00Z'),
        'validTo' => null,
        'relationshipStartNode' => ['id' => '52990021U40DYSDLV579', 'type' => 'LEI'],
        'relationshipEndNode' => ['id' => '7LTWFZYICNSX8D621K86', 'type' => 'LEI'],
        'relationshipType' => 'IS_DIRECTLY_CONSOLIDATED_BY',
        'relationshipStatus' => 'ACTIVE',
        'registrationStatus' => 'LAPSED',
        'registrationManagingLou' => '5299000J2N45DDNE4Y28',
        'registrationCorroborationDocuments' => 'ACCOUNTS_FILING',
        'registrationCorroborationReference' => null,
        'extension' => ['deletedAt' => null],
    ]],
    'ReportingException' => [ReportingException::fromResponse(...), 'reporting-exception', [
        'lei' => '7LTWFZYICNSX8D621K86',
        'category' => 'DIRECT_ACCOUNTING_CONSOLIDATION_PARENT',
        'reason' => 'NO_KNOWN_PERSON',
        'reference' => null,
        'validFrom' => null,
        'validTo' => null,
    ]],
    'VLeiIssuer' => [VLeiIssuer::fromResponse(...), 'vlei-issuer', [
        'id' => '300300CQ1FG1K4KM7075',
        'lei' => '300300CQ1FG1K4KM7075',
        'name' => '中金金融认证中心有限公司',
        'marketingName' => 'CFCA',
        'website' => 'https://www.cfca.com.cn/',
        'qualificationDate' => CarbonImmutable::parse('2025-02-14T00:00:00+00:00'),
    ]],
]);

test('missing or unknown values fall back to safe defaults', function (): void {
    $record = LeiRecord::fromResponse(['id' => 'X', 'attributes' => ['entity' => ['category' => 'NEW_CATEGORY'], 'bic' => ['A', null]]]);
    $modification = FieldModification::fromResponse([]);

    expect($record)->toHaveProperties([
        'entityLegalName' => null,
        'entityCategory' => null,
        'registrationStatus' => null,
        'entityCreationDate' => null,
        'bic' => ['A'],
        'mic' => null,
    ])->and($modification)->toHaveProperties([
        'recordType' => RecordType::Lei,
        'modificationType' => ModificationType::Update,
        'date' => null,
    ]);
});

test('dates do not depend on the app timezone', function (): void {
    date_default_timezone_set('Pacific/Auckland');

    $form = EntityLegalForm::fromResponse(gleifFixture('entity-legal-form'));
    $issuer = LeiIssuer::fromResponse(gleifFixture('lei-issuer'));

    expect($form->dateCreated?->toIso8601String())->toBe('2017-11-30T00:00:00+00:00')
        ->and($issuer->accreditationDate?->toIso8601String())->toBe('2017-04-13T00:00:00+00:00');
});
