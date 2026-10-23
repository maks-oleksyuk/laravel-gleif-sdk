<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Enums;

enum ModificationType: string
{
    case Initial = 'INITIAL';

    case Insert = 'INSERT';

    case Delete = 'DELETE';

    case Update = 'UPDATE';
}
