<?php

namespace Botble\ThemeGenerator\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\ThemeGenerator\Commands\ThemeCreateCommand;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ThemeCreateCommand::class,
            ]);
        }
    }
}
