<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Parents;

use Oleksyuk\Gleif\Data\LeiRecord;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class UltimateParentLeiRecord extends GleifRequest
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/lei-records/'.rawurlencode($this->id).'/ultimate-parent';
    }

    public function createDtoFromResponse(Response $response): LeiRecord
    {
        return self::mapOne($response, LeiRecord::fromResponse(...));
    }
}
