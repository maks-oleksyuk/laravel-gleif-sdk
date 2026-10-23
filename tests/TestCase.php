<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Tests;

use Oleksyuk\Gleif\GleifServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Saloon\Http\Faking\MockClient;

abstract class TestCase extends Orchestra
{
    protected function tearDown(): void
    {
        MockClient::destroyGlobal();

        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [
            GleifServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('cache.default', 'array');
    }
}
