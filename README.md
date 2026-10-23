<div align="center">

# laravel-gleif-sdk

[![CI](https://img.shields.io/github/actions/workflow/status/maks-oleksyuk/laravel-gleif-sdk/ci.yml?branch=main&style=flat&label=CI)](//github.com/maks-oleksyuk/laravel-gleif-sdk/actions/workflows/ci.yml)
[![Coverage](https://img.shields.io/codecov/c/github/maks-oleksyuk/laravel-gleif-sdk?style=flat&logo=codecov&logoColor=white)](//codecov.io/gh/maks-oleksyuk/laravel-gleif-sdk)
[![Latest Version](https://img.shields.io/packagist/v/oleksyuk/laravel-gleif-sdk.svg?style=flat&logo=packagist&logoColor=white&color=F28D1A)](//packagist.org/packages/oleksyuk/laravel-gleif-sdk)
[![PHP Version](https://img.shields.io/badge/PHP-8.4%2B-777bb4?style=flat&logo=php&logoColor=white)](composer.json)
[![Laravel Version](https://img.shields.io/badge/Laravel-13-ff2d20?style=flat&logo=laravel&logoColor=white)](composer.json)
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

Every method returns a Saloon `Response`: call `->dto()` for typed objects from `Oleksyuk\Gleif\Data`, or `->json()` for the raw payload. GLEIF answers `404` when a related record doesn't exist (e.g. an entity without a direct parent), so check `->failed()` or use `->dtoOrFail()` there. The facade proxies to `Oleksyuk\Gleif\GleifConnector`, a container singleton, which can be injected instead.

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
| `export()`                     | LEI record export (`csv`, `xlsx`, `json`, `xml`)                             |

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

`all()` returns one page. To walk every page lazily, pass a request to `paginate()`:

```php
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\AllLeiRecords;

$paginator = Gleif::paginate(new AllLeiRecords(entityLegalAddressCountry: 'UA'))->setPerPageLimit(200);

foreach ($paginator->items() as $item) {
    // raw record array
}
```

### Caching

Responses are cached in your Laravel cache until shortly after the next [Golden Copy](//www.gleif.org/en/lei-data/gleif-golden-copy) publication (02:15, 10:15 and 18:15 UTC), when GLEIF data can change. Pick a dedicated store via `.env`:

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
