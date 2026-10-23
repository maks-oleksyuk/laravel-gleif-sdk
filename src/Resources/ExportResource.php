<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Requests\Export\Export;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class ExportResource extends BaseResource
{
    /**
     * @param  'csv'|'xlsx'|'json'|'xml'  $format
     * @param  string[]  $lei
     */
    public function leiRecords(string $format, array $lei = []): Response
    {
        return $this->connector->send(new Export($format, $lei));
    }
}
