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
 * "METHOD /path" of every Request in src/Requests, built with "{}" (["{}"] for an array, the first case for an enum) for each required argument.
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
        $arguments = array_map(function (ReflectionParameter $parameter): mixed {
            $type = (string) $parameter->getType();

            return match (true) {
                $type === 'array' => ['{}'],
                enum_exists($type) => new ReflectionEnum($type)->getCases()[0]->getValue(),
                default => '{}',
            };
        }, $required);
        $request = new $class(...$arguments);

        $endpoint = $request->resolveEndpoint();
        $url = str_starts_with($endpoint, 'https://') ? $endpoint : $baseUrl.$endpoint;
        $path = rawurldecode((string) parse_url($url, PHP_URL_PATH));

        // An enum argument (e.g. the export format) fills a path variable too.
        foreach ($arguments as $argument) {
            if ($argument instanceof BackedEnum) {
                $path = (string) preg_replace('#(?<=[/.])'.preg_quote((string) $argument->value, '#').'(?=/|$)#', '{}', $path);
            }
        }

        $operations[$class] = $request->getMethod()->value.' '.$path;
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
