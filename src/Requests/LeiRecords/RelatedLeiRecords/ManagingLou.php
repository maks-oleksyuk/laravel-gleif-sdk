<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiRecords\RelatedLeiRecords;

use Oleksyuk\Gleif\Concerns\HasGleifCache;
use Oleksyuk\Gleif\Concerns\MapsResponseData;
use Oleksyuk\Gleif\Data\LeiRecord;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class ManagingLou extends Request implements Cacheable
{
    use HasGleifCache;
    use MapsResponseData;

    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/lei-records/'.$this->id.'/managing-lou';
    }

    public function createDtoFromResponse(Response $response): LeiRecord
    {
        return self::mapOne($response, LeiRecord::fromResponse(...));
    }
}
