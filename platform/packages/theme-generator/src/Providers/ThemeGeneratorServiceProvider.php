<?php

namespace Botble\ThemeGenerator\Providers;

use Botble\Base\Supports\ServiceProvider;

class ThemeGeneratorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->register(CommandServiceProvider::class);
    }
}
