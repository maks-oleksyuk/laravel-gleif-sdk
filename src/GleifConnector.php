<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif;

use Illuminate\Support\Facades\Cache;
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
use Saloon\Contracts\Sender;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\PagedPaginator;
use Saloon\PaginationPlugin\Paginator;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Limit;
use Saloon\RateLimitPlugin\Stores\LaravelCacheStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;

final class GleifConnector extends Connector implements HasPagination
{
    use HasRateLimits;

    public function resolveBaseUrl(): string
    {
        return 'https://api.gleif.org/api/v1';
    }

    public function leiRecord(): LeiRecordResource
    {
        return new LeiRecordResource($this);
    }

    public function leiIssuer(): LeiIssuerResource
    {
        return new LeiIssuerResource($this);
    }

    public function vLeiIssuer(): VLeiIssuerResource
    {
        return new VLeiIssuerResource($this);
    }

    public function fuzzyCompletion(): FuzzyCompletionResource
    {
        return new FuzzyCompletionResource($this);
    }

    public function autoCompletion(): AutoCompletionResource
    {
        return new AutoCompletionResource($this);
    }

    public function field(): FieldResource
    {
        return new FieldResource($this);
    }

    public function country(): CountryResource
    {
        return new CountryResource($this);
    }

    public function entityLegalForm(): EntityLegalFormResource
    {
        return new EntityLegalFormResource($this);
    }

    public function officialOrganizationalRole(): OfficialOrganizationalRoleResource
    {
        return new OfficialOrganizationalRoleResource($this);
    }

    public function jurisdiction(): JurisdictionResource
    {
        return new JurisdictionResource($this);
    }

    public function region(): RegionResource
    {
        return new RegionResource($this);
    }

    public function registrationAuthority(): RegistrationAuthorityResource
    {
        return new RegistrationAuthorityResource($this);
    }

    public function registrationAgent(): RegistrationAgentResource
    {
        return new RegistrationAgentResource($this);
    }

    public function export(): ExportResource
    {
        return new ExportResource($this);
    }

    public function paginate(Request $request): Paginator
    {
        return new class(connector: $this, request: $request) extends PagedPaginator
        {
            protected function isLastPage(Response $response): bool
            {
                return is_null($response->json('links.next'));
            }

            protected function getPageItems(Response $response, Request $request): array
            {
                $data = $response->json('data');

                return is_array($data) ? $data : [];
            }

            protected function applyPagination(Request $request): Request
            {
                $request->query()->add('page[number]', $this->currentPage + 1);

                if ($this->perPageLimit !== null) {
                    $request->query()->add('page[size]', $this->perPageLimit);
                }

                return $request;
            }
        };
    }

    /**
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/vnd.api+json',
        ];
    }

    protected function defaultSender(): Sender
    {
        $sender = resolve('gleif.sender');

        if (! $sender instanceof Sender) {
            throw new \RuntimeException('Gleif sender not found in container');
        }

        return $sender;
    }

    /**
     * @return Limit[]
     */
    protected function resolveLimits(): array
    {
        return [
            Limit::allow(60)->everyMinute()->sleep(),
        ];
    }

    protected function getTooManyAttemptsLimiter(): Limit
    {
        return Limit::custom($this->handleTooManyAttempts(...))->sleep();
    }

    protected function resolveRateLimitStore(): RateLimitStore
    {
        /** @var string|null $store */
        $store = config('gleif.cache.store', config('cache.default'));

        return new LaravelCacheStore(
            Cache::store($store)
        );
    }
}
