<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Enums;

/**
 * @see https://gleif.org/en/lei-data/access-and-use-lei-data/policy-conformity-flag
 */
enum PolicyConformityFlag: string
{
    /**
     * LEI record is timely renewed and Level 2 data reporting is complete.
     */
    case Conforming = 'CONFORMING';

    /**
     * LEI record is not timely renewed and/or Level 2 data reporting is incomplete.
     */
    case NonConforming = 'NON_CONFORMING';

    /**
     * LEI record is RETIRED, ANNULLED, or DUPLICATE.
     */
    case NotApplicable = 'NOT_APPLICABLE';

    /**
     * Newly added LEI records whose conformity value is not yet determined.
     */
    case Undetermined = 'NULL';
}
