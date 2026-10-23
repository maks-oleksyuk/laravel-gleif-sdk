<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Enums;

enum RegistrationStatus: string
{
    case Issued = 'ISSUED';

    case Lapsed = 'LAPSED';

    case Annulled = 'ANNULLED';

    case PendingTransfer = 'PENDING_TRANSFER';

    case PendingArchival = 'PENDING_ARCHIVAL';

    case Duplicate = 'DUPLICATE';

    case Retired = 'RETIRED';

    case Merged = 'MERGED';
}
