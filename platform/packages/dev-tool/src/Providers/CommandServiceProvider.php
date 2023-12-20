<?php

namespace Botble\DevTool\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\DevTool\Commands\LocaleCreateCommand;
use Botble\DevTool\Commands\LocaleRemoveCommand;
use Botble\DevTool\Commands\Make\ControllerMakeCommand;
use Botble\DevTool\Commands\Make\FormMakeCommand;
use Botble\DevTool\Commands\Make\ModelMakeCommand;
use Botble\DevTool\Commands\Make\RepositoryMakeCommand;
use Botble\DevTool\Commands\Make\RequestMakeCommand;
use Botble\DevTool\Commands\Make\RouteMakeCommand;
use Botble\DevTool\Commands\Make\TableMakeCommand;
use Botble\DevTool\Commands\PackageCreateCommand;
use Botble\DevTool\Commands\PackageMakeCrudCommand;
use Botble\DevTool\Commands\PackageRemoveCommand;
use Botble\DevTool\Commands\RebuildPermissionsCommand;
use Botble\DevTool\Commands\TestSendMailCommand;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TableMakeCommand::class,
                ControllerMakeCommand::class,
                RouteMakeCommand::class,
                RequestMakeCommand::class,
                FormMakeCommand::class,
                ModelMakeCommand::class,
                RepositoryMakeCommand::class,
                PackageCreateCommand::class,
                PackageMakeCrudCommand::class,
                PackageRemoveCommand::class,
                TestSendMailCommand::class,
                RebuildPermissionsCommand::class,
                LocaleRemoveCommand::class,
                LocaleCreateCommand::class,
            ]);
        }
    }
}
