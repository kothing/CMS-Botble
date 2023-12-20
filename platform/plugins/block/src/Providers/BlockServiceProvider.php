<?php

namespace Botble\Block\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Block\Models\Block;
use Botble\Block\Repositories\Eloquent\BlockRepository;
use Botble\Block\Repositories\Interfaces\BlockInterface;
use Botble\CustomField\Facades\CustomField;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Illuminate\Routing\Events\RouteMatched;

class BlockServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(BlockInterface::class, function () {
            return new BlockRepository(new Block());
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/block')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadMigrations();

        $this->app['events']->listen(RouteMatched::class, function () {
            DashboardMenu::registerItem([
                'id' => 'cms-plugins-block',
                'priority' => 6,
                'parent_id' => null,
                'name' => 'plugins/block::block.menu',
                'icon' => 'fa fa-code',
                'url' => route('block.index'),
                'permissions' => ['block.index'],
            ]);
        });

        if (defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
            LanguageAdvancedManager::registerModule(Block::class, [
                'name',
                'description',
                'content',
            ]);
        }

        $this->app->booted(function () {
            if (defined('CUSTOM_FIELD_MODULE_SCREEN_NAME')) {
                CustomField::registerModule(Block::class)
                    ->registerRule('basic', trans('plugins/block::block.name'), Block::class, function () {
                        return $this->app->make(BlockInterface::class)
                            ->getModel()
                            ->select([
                                'id',
                                'name',
                            ])
                            ->orderBy('created_at', 'DESC')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->expandRule('other', trans('plugins/custom-field::rules.model_name'), 'model_name', function () {
                        return [
                            Block::class => trans('plugins/block::block.name'),
                        ];
                    });
            }

            $this->app->register(HookServiceProvider::class);
        });
    }
}
