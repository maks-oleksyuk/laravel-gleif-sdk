<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif;

use Illuminate\Support\ServiceProvider;
use Saloon\Http\Senders\GuzzleSender;

final class GleifServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/gleif.php', 'gleif');

        $this->app->singleton('gleif.sender', fn (): GuzzleSender => new GuzzleSender);

        $this->app->singleton(GleifConnector::class);

        $this->app->alias(GleifConnector::class, 'gleif');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/gleif.php' => config_path('gleif.php'),
            ], 'gleif-config');
        }
    }
}
