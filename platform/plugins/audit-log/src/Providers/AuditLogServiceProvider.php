<?php

namespace Botble\AuditLog\Providers;

use Botble\AuditLog\Facades\AuditLog;
use Botble\AuditLog\Models\AuditHistory;
use Botble\AuditLog\Repositories\Eloquent\AuditLogRepository;
use Botble\AuditLog\Repositories\Interfaces\AuditLogInterface;
use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Events\RouteMatched;

/**
 * @since 02/07/2016 09:05 AM
 */
class AuditLogServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(AuditLogInterface::class, function () {
            return new AuditLogRepository(new AuditHistory());
        });

        AliasLoader::getInstance()->alias('AuditLog', AuditLog::class);
    }

    public function boot(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);

        $this->setNamespace('plugins/audit-log')
            ->loadHelpers()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->publishAssets();

        $this->app['events']->listen(RouteMatched::class, function () {
            DashboardMenu::registerItem([
                'id' => 'cms-plugin-audit-log',
                'priority' => 8,
                'parent_id' => 'cms-core-platform-administration',
                'name' => 'plugins/audit-log::history.name',
                'icon' => null,
                'url' => route('audit-log.index'),
                'permissions' => ['audit-log.index'],
            ]);
        });

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });

        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule
                ->command(PruneCommand::class, ['--model' => AuditHistory::class])
                ->dailyAt('00:30');
        });
    }
}
