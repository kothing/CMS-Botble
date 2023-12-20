<?php

namespace Botble\AuditLog\Providers;

use Botble\AuditLog\Commands\ActivityLogClearCommand;
use Botble\AuditLog\Commands\CleanOldLogsCommand;
use Botble\Base\Supports\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ActivityLogClearCommand::class,
                CleanOldLogsCommand::class,
            ]);
        }
    }
}
