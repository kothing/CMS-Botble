<?php

namespace Botble\Base\Providers;

use Botble\Base\Commands\ActivateLicenseCommand;
use Botble\Base\Commands\CleanupSystemCommand;
use Botble\Base\Commands\ClearExpiredCacheCommand;
use Botble\Base\Commands\ClearLogCommand;
use Botble\Base\Commands\ExportDatabaseCommand;
use Botble\Base\Commands\FetchGoogleFontsCommand;
use Botble\Base\Commands\InstallCommand;
use Botble\Base\Commands\PublishAssetsCommand;
use Botble\Base\Commands\UpdateCommand;
use Botble\Base\Supports\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            ActivateLicenseCommand::class,
            CleanupSystemCommand::class,
            ClearExpiredCacheCommand::class,
            ClearLogCommand::class,
            ExportDatabaseCommand::class,
            FetchGoogleFontsCommand::class,
            InstallCommand::class,
            PublishAssetsCommand::class,
            UpdateCommand::class,
        ]);

        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command(ClearExpiredCacheCommand::class)->everyFiveMinutes();
        });
    }
}
