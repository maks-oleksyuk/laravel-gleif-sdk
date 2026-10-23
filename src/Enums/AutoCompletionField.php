<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Enums;

enum AutoCompletionField: string
{
    /**
     * Search in all fields of the Legal Entity's LEI Record.
     */
    case Fulltext = 'fulltext';

    /**
     * Search in LEI Records of "child" LEI Records.
     */
    case Owns = 'owns';

    /**
     * Search in LEI Records of "parent" LEI Records.
     */
    case OwnedBy = 'ownedBy';
}
