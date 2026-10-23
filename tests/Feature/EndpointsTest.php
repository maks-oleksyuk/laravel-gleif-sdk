<?php

declare(strict_types=1);

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
use Oleksyuk\Gleif\Enums\AutoCompletionField;
use Oleksyuk\Gleif\Enums\EntityCategory;
use Oleksyuk\Gleif\Enums\ModificationType;
use Oleksyuk\Gleif\Enums\PolicyConformityFlag;
use Oleksyuk\Gleif\Enums\RecordType;
use Oleksyuk\Gleif\Enums\RegistrationStatus;
use Oleksyuk\Gleif\GleifConnector;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;

test('endpoint sends the right URL and maps the response', function (Closure $call, string $url, string $fixture, bool $isList, string $dto): void {
    $item = gleifFixture($fixture);
    $connector = new GleifConnector()->withMockClient(new MockClient([
        '*' => MockResponse::make(['data' => $isList ? [$item, 'not a resource object'] : $item]),
    ]));

    $response = $call($connector);
    assert($response instanceof Response && class_exists($dto));
    $result = $response->dto();

    expect(urldecode((string) $response->getPsrRequest()->getUri()))->toBe('https://api.gleif.org/api/v1'.$url)
        ->and($isList ? $result : [$result])->toHaveCount(1)->toContainOnlyInstancesOf($dto);
})->with([
    // LEI records
    'leiRecord()->all()' => [
        fn (GleifConnector $gleif): Response => $gleif->leiRecord()->all(
            lei: ['A', 'B'],
            bic: 'BIC',
            isin: 'ISIN',
            owns: 'OWNED',
            ownedBy: 'OWNER',
            entityCategory: EntityCategory::Fund,
            entityLegalName: 'Name',
            entityLegalAddressCountry: 'DE',
            registrationStatus: RegistrationStatus::Issued,
            conformityFlag: PolicyConformityFlag::Conforming,
            fulltext: 'text',
            sort: '-entity.legalName',
            pageSize: 10,
            pageNumber: 2,
        ),
        '/lei-records?filter[lei]=A,B&filter[bic]=BIC&filter[isin]=ISIN&filter[owns]=OWNED&filter[ownedBy]=OWNER'
        .'&filter[entity.category]=FUND&filter[entity.legalName]=Name&filter[entity.legalAddress.country]=DE'
        .'&filter[registration.status]=ISSUED&filter[conformityFlag]=CONFORMING&filter[fulltext]=text'
        .'&sort=-entity.legalName&page[size]=10&page[number]=2',
        'lei-record', true, LeiRecord::class,
    ],
    'leiRecord()->find()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->find('X'), '/lei-records/X', 'lei-record', false, LeiRecord::class],
    'leiRecord()->isins()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->isins('X', 5, 2), '/lei-records/X/isins?page[size]=5&page[number]=2', 'isin', true, Isin::class],
    'leiRecord()->leiIssuer()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->leiIssuer('X'), '/lei-records/X/lei-issuer', 'lei-issuer', false, LeiIssuer::class],
    'leiRecord()->directParent()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->directParent('X'), '/lei-records/X/direct-parent', 'lei-record', false, LeiRecord::class],
    'leiRecord()->directParentRelationship()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->directParentRelationship('X'), '/lei-records/X/direct-parent-relationship', 'relationship-record', false, RelationshipRecord::class],
    'leiRecord()->directParentReportingException()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->directParentReportingException('X'), '/lei-records/X/direct-parent-reporting-exception', 'reporting-exception', false, ReportingException::class],
    'leiRecord()->ultimateParent()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->ultimateParent('X'), '/lei-records/X/ultimate-parent', 'lei-record', false, LeiRecord::class],
    'leiRecord()->ultimateParentRelationship()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->ultimateParentRelationship('X'), '/lei-records/X/ultimate-parent-relationship', 'relationship-record', false, RelationshipRecord::class],
    'leiRecord()->ultimateParentReportingException()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->ultimateParentReportingException('X'), '/lei-records/X/ultimate-parent-reporting-exception', 'reporting-exception', false, ReportingException::class],
    'leiRecord()->directChildren()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->directChildren('X', 5, 2), '/lei-records/X/direct-children?page[size]=5&page[number]=2', 'lei-record', true, LeiRecord::class],
    'leiRecord()->directChildRelationships()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->directChildRelationships('X', 5, 2), '/lei-records/X/direct-child-relationships?page[size]=5&page[number]=2', 'relationship-record', true, RelationshipRecord::class],
    'leiRecord()->ultimateChildren()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->ultimateChildren('X', 5, 2), '/lei-records/X/ultimate-children?page[size]=5&page[number]=2', 'lei-record', true, LeiRecord::class],
    'leiRecord()->ultimateChildRelationships()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->ultimateChildRelationships('X', 5, 2), '/lei-records/X/ultimate-child-relationships?page[size]=5&page[number]=2', 'relationship-record', true, RelationshipRecord::class],
    'leiRecord()->associatedEntity()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->associatedEntity('X'), '/lei-records/X/associated-entity', 'lei-record', false, LeiRecord::class],
    'leiRecord()->managingLou()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->managingLou('X'), '/lei-records/X/managing-lou', 'lei-record', false, LeiRecord::class],
    'leiRecord()->successorEntity()' => [fn (GleifConnector $gleif): Response => $gleif->leiRecord()->successorEntity('X'), '/lei-records/X/successor-entity', 'lei-record', false, LeiRecord::class],
    'leiRecord()->fieldModifications()' => [
        fn (GleifConnector $gleif): Response => $gleif->leiRecord()->fieldModifications('X', ModificationType::Update, RecordType::Lei, 'field', '2026-01-01', 5, 2),
        '/lei-records/X/field-modifications?filter[modificationType]=UPDATE&filter[recordType]=LEI&filter[field]=field&filter[date]=2026-01-01&page[size]=5&page[number]=2',
        'field-modification', true, FieldModification::class,
    ],

    // Issuers
    'leiIssuer()->all()' => [fn (GleifConnector $gleif): Response => $gleif->leiIssuer()->all(5, 2), '/lei-issuers?page[size]=5&page[number]=2', 'lei-issuer', true, LeiIssuer::class],
    'leiIssuer()->find()' => [fn (GleifConnector $gleif): Response => $gleif->leiIssuer()->find('X'), '/lei-issuers/X', 'lei-issuer', false, LeiIssuer::class],
    'leiIssuer()->jurisdictions()' => [fn (GleifConnector $gleif): Response => $gleif->leiIssuer()->jurisdictions('X'), '/lei-issuers/X/jurisdictions', 'lei-issuer-jurisdiction', true, LeiIssuerJurisdiction::class],
    'vLeiIssuer()->all()' => [fn (GleifConnector $gleif): Response => $gleif->vLeiIssuer()->all(5, 2), '/vlei-issuers?page[size]=5&page[number]=2', 'vlei-issuer', true, VLeiIssuer::class],
    'vLeiIssuer()->find()' => [fn (GleifConnector $gleif): Response => $gleif->vLeiIssuer()->find('X'), '/vlei-issuers/X', 'vlei-issuer', false, VLeiIssuer::class],

    // Search
    'fuzzyCompletion()->search()' => [fn (GleifConnector $gleif): Response => $gleif->fuzzyCompletion()->search('gleif'), '/fuzzycompletions?q=gleif&field=entity.legalName', 'fuzzycompletion', true, FuzzyCompletion::class],
    'autoCompletion()->search()' => [fn (GleifConnector $gleif): Response => $gleif->autoCompletion()->search('gleif', AutoCompletionField::Owns), '/autocompletions?q=gleif&field=owns', 'autocompletion', true, AutoCompletion::class],

    // Reference data
    'field()->all()' => [fn (GleifConnector $gleif): Response => $gleif->field()->all(5, 2), '/fields?page[size]=5&page[number]=2', 'field', true, Field::class],
    'field()->find()' => [fn (GleifConnector $gleif): Response => $gleif->field()->find('X'), '/fields/X', 'field', false, Field::class],
    'country()->all()' => [fn (GleifConnector $gleif): Response => $gleif->country()->all(5, 2), '/countries?page[size]=5&page[number]=2', 'country', true, Country::class],
    'country()->find()' => [fn (GleifConnector $gleif): Response => $gleif->country()->find('DE'), '/countries/DE', 'country', false, Country::class],
    'region()->all()' => [fn (GleifConnector $gleif): Response => $gleif->region()->all(5, 2), '/regions?page[size]=5&page[number]=2', 'region', true, Region::class],
    'region()->find()' => [fn (GleifConnector $gleif): Response => $gleif->region()->find('AD-02'), '/regions/AD-02', 'region', false, Region::class],
    'jurisdiction()->all()' => [fn (GleifConnector $gleif): Response => $gleif->jurisdiction()->all(5, 2), '/jurisdictions?page[size]=5&page[number]=2', 'jurisdiction', true, Jurisdiction::class],
    'jurisdiction()->find()' => [fn (GleifConnector $gleif): Response => $gleif->jurisdiction()->find('DE'), '/jurisdictions/DE', 'jurisdiction', false, Jurisdiction::class],
    'entityLegalForm()->all()' => [fn (GleifConnector $gleif): Response => $gleif->entityLegalForm()->all(5, 2), '/entity-legal-forms?page[size]=5&page[number]=2', 'entity-legal-form', true, EntityLegalForm::class],
    'entityLegalForm()->find()' => [fn (GleifConnector $gleif): Response => $gleif->entityLegalForm()->find('X'), '/entity-legal-forms/X', 'entity-legal-form', false, EntityLegalForm::class],
    'officialOrganizationalRole()->all()' => [
        fn (GleifConnector $gleif): Response => $gleif->officialOrganizationalRole()->all('Director', 'DE', 5, 2),
        '/official-organizational-roles?filter[name]=Director&filter[countryCode]=DE&page[size]=5&page[number]=2',
        'official-organizational-role', true, OfficialOrganizationalRole::class,
    ],
    'officialOrganizationalRole()->find()' => [fn (GleifConnector $gleif): Response => $gleif->officialOrganizationalRole()->find('X'), '/official-organizational-roles/X', 'official-organizational-role', false, OfficialOrganizationalRole::class],
    'registrationAuthority()->all()' => [fn (GleifConnector $gleif): Response => $gleif->registrationAuthority()->all(5, 2), '/registration-authorities?page[size]=5&page[number]=2', 'registration-authority', true, RegistrationAuthority::class],
    'registrationAuthority()->find()' => [fn (GleifConnector $gleif): Response => $gleif->registrationAuthority()->find('X'), '/registration-authorities/X', 'registration-authority', false, RegistrationAuthority::class],
    'registrationAgent()->all()' => [fn (GleifConnector $gleif): Response => $gleif->registrationAgent()->all('LEI', 'ISSUER'), '/registration-agents?filter[lei]=LEI&filter[leiIssuer]=ISSUER', 'registration-agent', true, RegistrationAgent::class],
    'registrationAgent()->find()' => [fn (GleifConnector $gleif): Response => $gleif->registrationAgent()->find('X'), '/registration-agents/X', 'registration-agent', false, RegistrationAgent::class],
]);

test('optional filters are left out of the query', function (): void {
    $connector = new GleifConnector()->withMockClient(new MockClient(['*' => MockResponse::make(['data' => []])]));

    $response = $connector->leiRecord()->all();

    expect($response->getPsrRequest()->getUri()->getQuery())->toBeEmpty();
});

test('a list endpoint without a data array maps to no DTOs', function (): void {
    $connector = new GleifConnector()->withMockClient(new MockClient(['*' => MockResponse::make(['errors' => []], 500)]));

    expect($connector->country()->all()->dto())->toBe([]);
});

test('a single-record endpoint without a data object throws', function (): void {
    $connector = new GleifConnector()->withMockClient(new MockClient(['*' => MockResponse::make(['errors' => [['status' => '404']]], 404)]));

    $connector->leiRecord()->directParent('X')->dto();
})->throws(RuntimeException::class, 'Invalid response data');

test('export() downloads LEI records from the export API', function (): void {
    $mockClient = new MockClient(['*' => MockResponse::make("LEI,Name\nX,Y\n")]);

    $response = new GleifConnector()->withMockClient($mockClient)->export()->leiRecords('csv', ['A', 'B']);

    expect(urldecode((string) $response->getPsrRequest()->getUri()))->toBe('https://api.gleif.org/export/v1/lei-records.csv?filter[lei]=A,B')
        ->and($response->body())->toBe("LEI,Name\nX,Y\n");
});
