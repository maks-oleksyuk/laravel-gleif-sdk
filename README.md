<div align="center">

# laravel-gleif-sdk

[![CI](https://img.shields.io/github/actions/workflow/status/maks-oleksyuk/laravel-gleif-sdk/ci.yml?branch=main&style=flat&label=CI)](//github.com/maks-oleksyuk/laravel-gleif-sdk/actions/workflows/ci.yml)
[![Coverage](https://img.shields.io/codecov/c/github/maks-oleksyuk/laravel-gleif-sdk?style=flat&logo=codecov&logoColor=white)](//codecov.io/gh/maks-oleksyuk/laravel-gleif-sdk)
[![PHP Version](https://img.shields.io/badge/PHP-8.4%2B-777bb4?style=flat&logo=php&logoColor=white)](composer.json)
[![Laravel Version](https://img.shields.io/badge/Laravel-13-ff2d20?style=flat&logo=laravel&logoColor=white)](composer.json)
[![Latest Version](https://img.shields.io/packagist/v/oleksyuk/laravel-gleif-sdk.svg?style=flat&logo=packagist&logoColor=white&color=F28D1A)](//packagist.org/packages/oleksyuk/laravel-gleif-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/oleksyuk/laravel-gleif-sdk.svg?style=flat&logo=packagist&logoColor=white&color=F28D1A)](//packagist.org/packages/oleksyuk/laravel-gleif-sdk/stats)

Laravel SDK for the [GLEIF](//www.gleif.org) LEI API, built on [Saloon](//docs.saloon.dev).

</div>

## Installation

```bash
composer require oleksyuk/laravel-gleif-sdk
```

The service provider and the `Gleif` facade are auto-discovered. The GLEIF API is public, so there are no credentials to configure.

## Usage

```php
use Oleksyuk\Gleif\Facades\Gleif;

$record = Gleif::leiRecord()->find('984500A8D612DCC67997')->dto();

echo $record->entityLegalName['name']; // Global Legal Entity Identifier Foundation
```

Every method returns a Saloon `Response`: call `->dto()` for typed objects from `Oleksyuk\Gleif\Data`, or `->json()` for the raw payload. On an error response `->dto()` throws Saloon's `RequestException` instead of mapping it: e.g. `NotFoundException` when a related record doesn't exist (an entity without a direct parent) or `BadRequestException` for a `pageSize` above 200. An empty, `.` or `..` ID throws `InvalidArgumentException` before anything is sent. The facade proxies to `Oleksyuk\Gleif\GleifConnector`, which can be injected instead. It is scoped, so a mock client or header added to it doesn't leak into the next Octane request or queued job.

### Covered APIs

| Method                         | GLEIF API                                                                    |
|--------------------------------|------------------------------------------------------------------------------|
| `leiRecord()`                  | LEI records, ISINs, level 2 parents/children, related records, modifications |
| `leiIssuer()`                  | LEI issuers (LOUs) and their jurisdictions                                   |
| `vLeiIssuer()`                 | vLEI issuers                                                                 |
| `fuzzyCompletion()`            | Fuzzy completions                                                            |
| `autoCompletion()`             | Auto completions                                                             |
| `field()`                      | Fields                                                                       |
| `country()`                    | Countries                                                                    |
| `region()`                     | Regions                                                                      |
| `jurisdiction()`               | Jurisdictions                                                                |
| `entityLegalForm()`            | Entity legal forms (ELF)                                                     |
| `officialOrganizationalRole()` | Official organizational roles (OOR)                                          |
| `registrationAuthority()`      | Registration authorities                                                     |
| `registrationAgent()`          | Registration agents                                                          |
| `export()`                     | Export of 1–5,000 LEI records in an `ExportFormat` (CSV, XLSX, JSON, XML)    |

### Searching

```php
use Oleksyuk\Gleif\Enums\FuzzyCompletionField;
use Oleksyuk\Gleif\Enums\RegistrationStatus;

$records = Gleif::leiRecord()->all(
    entityLegalName: 'Global Legal Entity',
    registrationStatus: RegistrationStatus::Issued,
    pageSize: 50,
)->dto();

$suggestions = Gleif::fuzzyCompletion()->search('gleif', FuzzyCompletionField::EntityLegalName)->dto();
```

### Pagination

`all()` returns one page (`pageSize` is at most 200, and GLEIF caps page numbers at 10,000 results). To walk every page lazily, pass a request to `paginate()`: it follows the `links.next` GLEIF returns and uses cursor pagination for LEI records, so it isn't capped:

```php
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\AllLeiRecords;

$paginator = Gleif::paginate(new AllLeiRecords(entityLegalAddressCountry: 'UA'))->setPerPageLimit(200);

foreach ($paginator->items() as $item) {
    // $item is an Oleksyuk\Gleif\Data\LeiRecord, as from ->dto()
}
```

### Caching

GLEIF publishes a new [Golden Copy](//www.gleif.org/en/lei-data/gleif-golden-copy) three times a day, so responses are cached in your Laravel cache until the next refresh window (02:15, 10:15 and 18:15 UTC). That includes `404` answers, such as an unknown LEI or an entity without a parent: they don't change in between either. To skip a request that can only answer `404`, check the record first: `$record->hasDirectParent` and `$record->hasUltimateParent` tell whether GLEIF links a parent record or reports an exception instead. Pick a dedicated store via `.env`:

```dotenv
GLEIF_CACHE_STORE=redis
```

It falls back to `cache.default`. To publish the config file:

```bash
php artisan vendor:publish --tag=gleif-config
```

To skip the cache for one request, send the request yourself:

```php
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\LeiRecordById;

Gleif::send(new LeiRecordById('984500A8D612DCC67997')->disableCaching());
```

### Rate limit

GLEIF allows 60 requests per minute. The connector tracks that limit in the same cache store and **sleeps** until a slot frees up instead of failing; a `429` from GLEIF is handled the same way, using `Retry-After`. Cached responses don't count against the limit. Keep that in mind in web requests: a burst of uncached calls can block for up to a minute.

### Testing your code

Use Saloon's mocking: no request leaves your app.

```php
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\LeiRecordById;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

MockClient::global([
    LeiRecordById::class => MockResponse::make(['data' => [/* ... */]]),
]);
```

## Development

```bash
task lint            # PHPStan (max level), Pint, Rector, composer validate
task test            # Pest
task test:coverage   # Pest, fails below 100% code coverage; HTML report in build/coverage (needs Xdebug or PCOV)
task test:types      # Pest, fails below 100% type coverage
task fix             # auto-fix Rector / Pint issues
task contract:update # refresh the GLEIF endpoint snapshot used by ContractTest
task fixtures:update # refresh the real GLEIF responses in tests/Fixtures
```

GLEIF publishes no OpenAPI spec, so `tests/Contract/gleif-api.json` is a snapshot of the endpoints in its [Postman collection](//api.gleif.org/docs): `ContractTest` checks that every request hits a documented endpoint and every documented endpoint has a request. Fixtures in `tests/Fixtures` are real GLEIF API resource objects, so the DTO tests check the mapping against what the API actually returns. Both refresh commands need network access; review their diff before committing.
