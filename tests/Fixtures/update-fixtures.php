<?php

declare(strict_types=1);

/*
 * Refreshes the fixtures from the live GLEIF API: `task fixtures:update`.
 * Each fixture is one real resource object, so the diff of a refresh shows what GLEIF changed
 * in its responses, and DataTest fails when a field the DTOs read was renamed or removed.
 * Sources point at stable records (by ID or narrowed by filters) so a refresh doesn't churn.
 */

const API = 'https://api.gleif.org/api/v1/';
const DEUTSCHE_BANK = '7LTWFZYICNSX8D621K86';

/** @var array<string, string> $fixtures fixture name => API path; list endpoints contribute their first item */
$fixtures = [
    'autocompletion' => 'autocompletions?field=fulltext&q=gleif',
    'country' => 'countries/DE',
    'entity-legal-form' => 'entity-legal-forms/6QQB',
    'field' => 'fields/LEIREC_BRANCH_ENTITY_STATUS',
    'field-modification' => 'lei-records/'.DEUTSCHE_BANK.'/field-modifications?filter[date]=2026-04-07T16:00:00Z'
        .'&filter[field]=/lei:LEIData/lei:LEIRecords/lei:LEIRecord/lei:Registration/lei:NextRenewalDate',
    'fuzzycompletion' => 'fuzzycompletions?field=entity.legalName&q=gleif',
    'isin' => 'lei-records/'.DEUTSCHE_BANK.'/isins?page[size]=1',
    'jurisdiction' => 'jurisdictions/DE',
    'lei-issuer' => 'lei-issuers/5299000J2N45DDNE4Y28',
    'lei-issuer-jurisdiction' => 'lei-issuers/5299000J2N45DDNE4Y28/jurisdictions',
    'lei-record' => 'lei-records/'.DEUTSCHE_BANK,
    'official-organizational-role' => 'official-organizational-roles/01D0O4',
    'region' => 'regions/AD-02',
    'registration-agent' => 'registration-agents/5d10d4dc929ab6.72309473',
    'registration-authority' => 'registration-authorities/RA000242',
    'relationship-record' => 'lei-records/52990021U40DYSDLV579/direct-parent-relationship',
    'reporting-exception' => 'lei-records/'.DEUTSCHE_BANK.'/direct-parent-reporting-exception',
    'vlei-issuer' => 'vlei-issuers/300300CQ1FG1K4KM7075',
];

$context = stream_context_create(['http' => ['header' => "Accept: application/vnd.api+json\r\n"]]);

foreach ($fixtures as $name => $path) {
    $json = file_get_contents(API.$path, context: $context);
    if ($json === false) {
        throw new RuntimeException("Could not fetch {$path}.");
    }

    $body = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    $data = is_array($body) ? ($body['data'] ?? null) : null;
    $item = is_array($data) && array_is_list($data) ? ($data[0] ?? null) : $data;
    if (! is_array($item)) {
        throw new RuntimeException("No resource object in {$path}.");
    }

    // Links only repeat the URL; keep relationships that carry related IDs.
    unset($item['links']);
    if (is_array($item['relationships'] ?? null)) {
        $item['relationships'] = array_filter(array_map(
            fn (mixed $relation): mixed => is_array($relation) ? array_diff_key($relation, ['links' => true]) : $relation,
            $item['relationships'],
        ));
    }

    if (($item['relationships'] ?? null) === []) {
        unset($item['relationships']);
    }

    // Deutsche Bank has dozens of BICs and merger events: a few are enough to test the mapping.
    if ($name === 'lei-record' && is_array($item['attributes'] ?? null)) {
        $attributes = $item['attributes'];
        $attributes['bic'] = array_slice(is_array($attributes['bic'] ?? null) ? $attributes['bic'] : [], 0, 3);
        if (is_array($attributes['entity'] ?? null) && is_array($attributes['entity']['eventGroups'] ?? null)) {
            $attributes['entity']['eventGroups'] = array_slice($attributes['entity']['eventGroups'], 0, 1);
        }

        $item['attributes'] = $attributes;
    }

    file_put_contents(__DIR__."/{$name}.json", json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n");
}

echo count($fixtures)." fixtures written.\n";
