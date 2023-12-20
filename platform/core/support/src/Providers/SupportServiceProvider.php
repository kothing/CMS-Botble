<?php

namespace Botble\Support\Providers;

use Botble\Base\Supports\ServiceProvider;

class SupportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app['files']->requireOnce(core_path('support/helpers/common.php'));
    }
}
