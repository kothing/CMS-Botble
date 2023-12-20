<?php

namespace Botble\Backup\Providers;

use Botble\Backup\Commands\BackupCleanCommand;
use Botble\Backup\Commands\BackupCreateCommand;
use Botble\Backup\Commands\BackupListCommand;
use Botble\Backup\Commands\BackupRemoveCommand;
use Botble\Backup\Commands\BackupRestoreCommand;
use Botble\Base\Supports\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BackupCreateCommand::class,
                BackupRestoreCommand::class,
                BackupRemoveCommand::class,
                BackupListCommand::class,
                BackupCleanCommand::class,
            ]);
        }
    }
}
