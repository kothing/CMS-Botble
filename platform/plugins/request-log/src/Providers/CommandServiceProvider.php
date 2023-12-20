<?php

namespace Botble\RequestLog\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\RequestLog\Commands\RequestLogClearCommand;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RequestLogClearCommand::class,
            ]);
        }
    }
}
