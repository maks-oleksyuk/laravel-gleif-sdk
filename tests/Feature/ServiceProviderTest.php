<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;
use Oleksyuk\Gleif\Facades\Gleif;
use Oleksyuk\Gleif\GleifConnector;
use Saloon\Http\Senders\GuzzleSender;

test('the connector is shared within a scope and reachable through the facade and alias', function (): void {
    expect(app(GleifConnector::class))->toBe(app('gleif'))
        ->and(Gleif::getFacadeRoot())->toBe(app(GleifConnector::class));
});

test('changes to the connector do not leak into the next Octane request or queued job', function (): void {
    $connector = Gleif::getFacadeRoot();

    app()->forgetScopedInstances();

    expect(Gleif::getFacadeRoot())->toBeInstanceOf(GleifConnector::class)->not->toBe($connector)
        ->and(app(GleifConnector::class))->toBe(Gleif::getFacadeRoot());
});

test('the connector sends through the shared Guzzle sender', function (): void {
    expect(new GleifConnector()->sender())->toBeInstanceOf(GuzzleSender::class)->toBe(app('gleif.sender'));
});

test('the connector refuses a gleif.sender binding that is not a Saloon sender', function (): void {
    app()->instance('gleif.sender', new stdClass);

    new GleifConnector()->sender();
})->throws(RuntimeException::class, 'Gleif sender not found in container');

test('the package config is merged and publishable', function (): void {
    expect(config()->has('gleif.cache.store'))->toBeTrue()
        ->and(ServiceProvider::pathsToPublish(group: 'gleif-config'))->toHaveCount(1);
});
