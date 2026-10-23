<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\EntityLegalForms;

use Oleksyuk\Gleif\Concerns\HasGleifCache;
use Oleksyuk\Gleif\Concerns\MapsResponseData;
use Oleksyuk\Gleif\Data\EntityLegalForm;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class EntityLegalFormById extends Request implements Cacheable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/entity-legal-forms/'.$this->id;
    }

    public function createDtoFromResponse(Response $response): EntityLegalForm
    {
        return self::mapOne($response, EntityLegalForm::fromResponse(...));
    }
}
