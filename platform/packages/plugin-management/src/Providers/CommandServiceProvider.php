<?php

namespace Botble\PluginManagement\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\PluginManagement\Commands\ClearCompiledCommand;
use Botble\PluginManagement\Commands\IlluminateClearCompiledCommand as OverrideIlluminateClearCompiledCommand;
use Botble\PluginManagement\Commands\PackageDiscoverCommand;
use Botble\PluginManagement\Commands\PluginActivateAllCommand;
use Botble\PluginManagement\Commands\PluginActivateCommand;
use Botble\PluginManagement\Commands\PluginAssetsPublishCommand;
use Botble\PluginManagement\Commands\PluginDeactivateAllCommand;
use Botble\PluginManagement\Commands\PluginDeactivateCommand;
use Botble\PluginManagement\Commands\PluginDiscoverCommand;
use Botble\PluginManagement\Commands\PluginInstallFromMarketplaceCommand;
use Botble\PluginManagement\Commands\PluginListCommand;
use Botble\PluginManagement\Commands\PluginRemoveAllCommand;
use Botble\PluginManagement\Commands\PluginRemoveCommand;
use Illuminate\Foundation\Console\ClearCompiledCommand as IlluminateClearCompiledCommand;
use Illuminate\Foundation\Console\PackageDiscoverCommand as IlluminatePackageDiscoverCommand;

class CommandServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->extend(IlluminatePackageDiscoverCommand::class, function () {
            return $this->app->make(PackageDiscoverCommand::class);
        });

        $this->app->extend(IlluminateClearCompiledCommand::class, function () {
            return $this->app->make(OverrideIlluminateClearCompiledCommand::class);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PluginAssetsPublishCommand::class,
                ClearCompiledCommand::class,
                PluginDiscoverCommand::class,
                PluginInstallFromMarketplaceCommand::class,
            ]);
        }

        $this->commands([
            PluginActivateCommand::class,
            PluginActivateAllCommand::class,
            PluginDeactivateCommand::class,
            PluginDeactivateAllCommand::class,
            PluginRemoveCommand::class,
            PluginRemoveAllCommand::class,
            PluginListCommand::class,
        ]);
    }
}
