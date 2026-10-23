<?php

declare(strict_types=1);

use Oleksyuk\Gleif\GleifConnector;
use Saloon\Http\Request;

/*
 * Checks the SDK against gleif-api.json, a snapshot of the endpoints in GLEIF's Postman
 * collection (refresh it with `task contract:update`): every request hits a documented
 * endpoint, and every documented endpoint has a request.
 */

/**
 * "METHOD /path" of every Request in src/Requests, built with "{}" (or ["{}"]) for each required argument.
 *
 * @return array<string, string> class => operation
 */
function sdkOperations(): array
{
    $baseUrl = new GleifConnector()->resolveBaseUrl();
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.'/../../src/Requests', FilesystemIterator::SKIP_DOTS));
    $operations = [];

    foreach ($files as $path => $file) {
        assert(is_string($path));
        $class = 'Oleksyuk\\Gleif\\Requests\\'.strtr(substr($path, strlen(__DIR__.'/../../src/Requests/'), -4), '/', '\\');
        assert(is_subclass_of($class, Request::class));

        $constructor = new ReflectionClass($class)->getConstructor();
        $required = array_filter($constructor?->getParameters() ?? [], fn (ReflectionParameter $parameter): bool => ! $parameter->isOptional());
        $request = new $class(...array_map(
            fn (ReflectionParameter $parameter): array|string => (string) $parameter->getType() === 'array' ? ['{}'] : '{}',
            $required,
        ));

        $endpoint = $request->resolveEndpoint();
        $url = str_starts_with($endpoint, 'https://') ? $endpoint : $baseUrl.$endpoint;
        $operations[$class] = $request->getMethod()->value.' '.rawurldecode((string) parse_url($url, PHP_URL_PATH));
    }

    return $operations;
}

/**
 * @return list<string> documented operations, with path variables as "{}"
 */
function documentedOperations(): array
{
    /** @var list<string> $operations */
    $operations = json_decode((string) file_get_contents(__DIR__.'/gleif-api.json'), true, flags: JSON_THROW_ON_ERROR);

    return array_map(fn (string $operation): string => (string) preg_replace('/\{\w+\}/', '{}', $operation), $operations);
}

test('every request targets an endpoint GLEIF documents', function (): void {
    expect(array_diff(sdkOperations(), documentedOperations()))->toBeEmpty();
});

test('every endpoint GLEIF documents has a request', function (): void {
    expect(array_values(array_diff(documentedOperations(), sdkOperations())))->toBeEmpty();
});
