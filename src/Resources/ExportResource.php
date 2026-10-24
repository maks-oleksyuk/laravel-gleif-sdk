<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Enums\ExportFormat;
use Oleksyuk\Gleif\Requests\Export\Export;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class ExportResource extends BaseResource
{
    /**
     * @param  string[]  $lei  1 to 5,000 LEIs
     *
     * @throws \InvalidArgumentException when there are no LEIs or more than 5,000
     */
    public function leiRecords(ExportFormat $format, array $lei): Response
    {
        return $this->connector->send(new Export($format, $lei));
    }
}
