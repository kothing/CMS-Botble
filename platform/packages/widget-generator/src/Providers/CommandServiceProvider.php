<?php

namespace Botble\WidgetGenerator\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\WidgetGenerator\Commands\WidgetCreateCommand;
use Botble\WidgetGenerator\Commands\WidgetRemoveCommand;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                WidgetCreateCommand::class,
                WidgetRemoveCommand::class,
            ]);
        }
    }
}
