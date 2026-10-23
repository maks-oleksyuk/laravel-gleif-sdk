<?php

declare(strict_types=1);

/*
 * Refreshes gleif-api.json from GLEIF's Postman collection, the only machine-readable API
 * description GLEIF publishes (api.gleif.org/docs redirects to it): `task contract:update`.
 * Only method + path are kept, so the diff of a refresh shows exactly which endpoints GLEIF
 * added, removed or renamed.
 */

const COLLECTION = 'https://documenter.gw.postman.com/api/collections/7679680/SVYrrxuU?segregateAuth=true&versionTag=latest';

$json = file_get_contents(COLLECTION);
if ($json === false) {
    throw new RuntimeException('Could not fetch the GLEIF Postman collection.');
}

$collection = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

/**
 * @return iterable<array<mixed>>
 */
function requests(mixed $node): iterable
{
    if (! is_array($node)) {
        return;
    }

    if (is_array($node['request'] ?? null)) {
        yield $node['request'];
    }

    foreach ($node as $child) {
        yield from requests($child);
    }
}

$operations = [];
foreach (requests($collection) as $request) {
    $url = $request['url'] ?? null;
    $method = $request['method'] ?? null;
    if (! is_string($url) || ! is_string($method)) {
        throw new RuntimeException('Unexpected request shape in the GLEIF Postman collection.');
    }

    if (parse_url($url, PHP_URL_HOST) !== 'api.gleif.org') {
        throw new RuntimeException("Unexpected host in {$url}.");
    }

    // ":id" path variables become "{id}"; the export URL is written as "lei-records./:format".
    $path = str_replace('./:', '.:', (string) parse_url($url, PHP_URL_PATH));
    $operations[] = strtoupper($method).' '.preg_replace('/:(\w+)/', '{$1}', $path);
}

$operations = array_values(array_unique($operations));
sort($operations);

file_put_contents(__DIR__.'/gleif-api.json', json_encode($operations, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n");
echo count($operations)." operations written.\n";
