<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Enums;

enum EntityCategory: string
{
    case Branch = 'BRANCH';

    case Fund = 'FUND';

    case SoleProprietor = 'SOLE_PROPRIETOR';

    case General = 'GENERAL';

    case ResidentGovernmentEntity = 'RESIDENT_GOVERNMENT_ENTITY';

    case InternationalOrganization = 'INTERNATIONAL_ORGANIZATION';
}
