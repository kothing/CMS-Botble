<?php

namespace Botble\PluginGenerator\Providers;

use Botble\Base\Supports\ServiceProvider;

class PluginGeneratorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->register(CommandServiceProvider::class);
    }
}
