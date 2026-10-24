<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Facades;

use Illuminate\Support\Facades\Facade;
use Oleksyuk\Gleif\GleifConnector;
use Oleksyuk\Gleif\Resources\AutoCompletionResource;
use Oleksyuk\Gleif\Resources\CountryResource;
use Oleksyuk\Gleif\Resources\EntityLegalFormResource;
use Oleksyuk\Gleif\Resources\ExportResource;
use Oleksyuk\Gleif\Resources\FieldResource;
use Oleksyuk\Gleif\Resources\FuzzyCompletionResource;
use Oleksyuk\Gleif\Resources\JurisdictionResource;
use Oleksyuk\Gleif\Resources\LeiIssuerResource;
use Oleksyuk\Gleif\Resources\LeiRecordResource;
use Oleksyuk\Gleif\Resources\OfficialOrganizationalRoleResource;
use Oleksyuk\Gleif\Resources\RegionResource;
use Oleksyuk\Gleif\Resources\RegistrationAgentResource;
use Oleksyuk\Gleif\Resources\RegistrationAuthorityResource;
use Oleksyuk\Gleif\Resources\VLeiIssuerResource;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Paginator;

/**
 * @method static AutoCompletionResource autoCompletion()
 * @method static CountryResource country()
 * @method static EntityLegalFormResource entityLegalForm()
 * @method static ExportResource export()
 * @method static FieldResource field()
 * @method static FuzzyCompletionResource fuzzyCompletion()
 * @method static JurisdictionResource jurisdiction()
 * @method static LeiIssuerResource leiIssuer()
 * @method static LeiRecordResource leiRecord()
 * @method static OfficialOrganizationalRoleResource officialOrganizationalRole()
 * @method static RegionResource region()
 * @method static RegistrationAgentResource registrationAgent()
 * @method static RegistrationAuthorityResource registrationAuthority()
 * @method static VLeiIssuerResource vLeiIssuer()
 * @method static Response send(Request $request, ?MockClient $mockClient = null, ?callable $handleRetry = null)
 * @method static Paginator paginate(Request $request)
 *
 * @see GleifConnector
 */
final class Gleif extends Facade
{
    /**
     * Resolve the scoped connector on every call: queue workers forget scoped instances
     * between jobs but keep the facade's cached one.
     *
     * @var bool
     */
    protected static $cached = false;

    protected static function getFacadeAccessor(): string
    {
        return GleifConnector::class;
    }
}
