<?php

namespace Botble\RequestLog\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\RequestLog\Models\RequestLog;
use Botble\RequestLog\Models\RequestLog as RequestLogModel;
use Botble\RequestLog\Repositories\Eloquent\RequestLogRepository;
use Botble\RequestLog\Repositories\Interfaces\RequestLogInterface;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Routing\Events\RouteMatched;

/**
 * @since 02/07/2016 09:50 AM
 */
class RequestLogServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(RequestLogInterface::class, function () {
            return new RequestLogRepository(new RequestLogModel());
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/request-log')
            ->loadHelpers()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->publishAssets();

        $this->app['events']->listen(RouteMatched::class, function () {
            DashboardMenu::registerItem([
                'id' => 'cms-plugin-request-log',
                'priority' => 8,
                'parent_id' => 'cms-core-platform-administration',
                'name' => 'plugins/request-log::request-log.name',
                'icon' => null,
                'url' => route('request-log.index'),
                'permissions' => ['request-log.index'],
            ]);
        });

        $this->app->register(EventServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });

        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command(PruneCommand::class, ['--model' => RequestLog::class])->dailyAt('00:30');
        });
    }
}
