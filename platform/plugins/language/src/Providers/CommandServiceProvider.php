<?php

namespace Botble\Language\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Language\Commands\RouteCacheCommand;
use Botble\Language\Commands\RouteClearCommand;
use Botble\Language\Commands\RouteTranslationsListCommand;
use Botble\Language\Commands\SyncOldDataCommand;
use Illuminate\Foundation\Console\RouteCacheCommand as BaseRouteCacheCommand;
use Illuminate\Foundation\Console\RouteClearCommand as BaseRouteClearCommand;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            SyncOldDataCommand::class,
            RouteTranslationsListCommand::class,
        ]);

        $this->app->extend(BaseRouteCacheCommand::class, function () {
            return new RouteCacheCommand($this->app['files']);
        });

        $this->app->extend(BaseRouteClearCommand::class, function () {
            return new RouteClearCommand($this->app['files']);
        });
    }
}
