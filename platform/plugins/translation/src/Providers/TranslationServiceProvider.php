<?php

namespace Botble\Translation\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Translation\Console\CleanCommand;
use Botble\Translation\Console\DownloadLocaleCommand;
use Botble\Translation\Console\ExportCommand;
use Botble\Translation\Console\ImportCommand;
use Botble\Translation\Console\RemoveLocaleCommand;
use Botble\Translation\Console\RemoveUnusedTranslationsCommand;
use Botble\Translation\Console\ResetCommand;
use Botble\Translation\Console\UpdateThemeTranslationCommand;
use Botble\Translation\Manager;
use Botble\Translation\Models\Translation;
use Botble\Translation\Repositories\Eloquent\TranslationRepository;
use Botble\Translation\Repositories\Interfaces\TranslationInterface;
use Illuminate\Routing\Events\RouteMatched;

class TranslationServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(TranslationInterface::class, function () {
            return new TranslationRepository(new Translation());
        });

        $this->app->bind('translation-manager', Manager::class);

        $this->commands([
            ImportCommand::class,
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ResetCommand::class,
                ExportCommand::class,
                CleanCommand::class,
                UpdateThemeTranslationCommand::class,
                RemoveUnusedTranslationsCommand::class,
                DownloadLocaleCommand::class,
                RemoveLocaleCommand::class,
            ]);
        }
    }

    public function boot(): void
    {
        $this->setNamespace('plugins/translation')
            ->loadAndPublishConfigurations(['general', 'permissions'])
            ->loadMigrations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishAssets();

        $this->app['events']->listen(RouteMatched::class, function () {
            DashboardMenu::make()
                ->registerItem([
                    'id' => 'cms-plugin-translation',
                    'priority' => 997,
                    'parent_id' => null,
                    'name' => 'plugins/translation::translation.translations',
                    'icon' => 'fas fa-language',
                    'url' => route('translations.index'),
                    'permissions' => ['translations.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugin-translation-locale',
                    'priority' => 1,
                    'parent_id' => 'cms-plugin-translation',
                    'name' => 'plugins/translation::translation.locales',
                    'icon' => null,
                    'url' => route('translations.locales'),
                    'permissions' => ['translations.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugin-translation-theme-translations',
                    'priority' => 2,
                    'parent_id' => 'cms-plugin-translation',
                    'name' => 'plugins/translation::translation.theme-translations',
                    'icon' => null,
                    'url' => route('translations.theme-translations'),
                    'permissions' => ['translations.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugin-translation-admin-translations',
                    'priority' => 3,
                    'parent_id' => 'cms-plugin-translation',
                    'name' => 'plugins/translation::translation.admin-translations',
                    'icon' => null,
                    'url' => route('translations.index'),
                    'permissions' => ['translations.index'],
                ]);
        });
    }
}
