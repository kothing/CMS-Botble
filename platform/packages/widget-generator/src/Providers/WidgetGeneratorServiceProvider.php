<?php

namespace Botble\WidgetGenerator\Providers;

use Botble\Base\Supports\ServiceProvider;

class WidgetGeneratorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->register(CommandServiceProvider::class);
    }
}
