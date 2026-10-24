<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Enums;

enum ExportFormat: string
{
    case Csv = 'csv';

    case Xlsx = 'xlsx';

    case Json = 'json';

    case Xml = 'xml';
}
