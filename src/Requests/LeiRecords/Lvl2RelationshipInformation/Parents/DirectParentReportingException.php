<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Parents;

use Oleksyuk\Gleif\Data\ReportingException;
use Oleksyuk\Gleif\GleifRequest;
use Saloon\Http\Response;

final class DirectParentReportingException extends GleifRequest
{
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/lei-records/'.rawurlencode($this->id).'/direct-parent-reporting-exception';
    }

    public function createDtoFromResponse(Response $response): ReportingException
    {
        return self::mapOne($response, ReportingException::fromResponse(...));
    }
}
