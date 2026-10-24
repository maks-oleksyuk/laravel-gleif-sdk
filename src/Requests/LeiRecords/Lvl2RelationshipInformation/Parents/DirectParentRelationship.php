<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Parents;

use Oleksyuk\Gleif\Data\RelationshipRecord;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class DirectParentRelationship extends GleifRequest
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/lei-records/'.rawurlencode($this->id).'/direct-parent-relationship';
    }

    public function createDtoFromResponse(Response $response): RelationshipRecord
    {
        return self::mapOne($response, RelationshipRecord::fromResponse(...));
    }
}
