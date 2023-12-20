<?php

namespace Botble\Backup\Providers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (BaseHelper::hasDemoModeEnabled()) {
            add_filter(DASHBOARD_FILTER_ADMIN_NOTIFICATIONS, [$this, 'registerAdminAlert'], 5);
        }
    }

    public function registerAdminAlert(string|null $alert): string
    {
        return $alert . view('plugins/backup::partials.admin-alert')->render();
    }
}
