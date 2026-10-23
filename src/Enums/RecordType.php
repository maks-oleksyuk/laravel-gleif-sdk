<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Enums;

enum RecordType: string
{
    case Lei = 'LEI';

    case Rr = 'RR';

    case Repex = 'REPEX';
}
