<?php

namespace Botble\Page\Providers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\Html;
use Botble\Base\Supports\RepositoryHelper;
use Botble\Base\Supports\ServiceProvider;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\Media\Facades\RvMedia;
use Botble\Menu\Facades\Menu;
use Botble\Page\Models\Page;
use Botble\Page\Services\PageService;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (defined('MENU_ACTION_SIDEBAR_OPTIONS')) {
            Menu::addMenuOptionModel(Page::class);
            add_action(MENU_ACTION_SIDEBAR_OPTIONS, [$this, 'registerMenuOptions'], 10);
        }

        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addPageStatsWidget'], 15, 2);
        add_filter(BASE_FILTER_PUBLIC_SINGLE_DATA, [$this, 'handleSingleView'], 1);

        if (function_exists('theme_option')) {
            add_action(RENDERING_THEME_OPTIONS_PAGE, [$this, 'addThemeOptions'], 31);
        }

        if (defined('THEME_FRONT_HEADER')) {
            add_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, function ($screen, $page): void {
                add_filter(THEME_FRONT_HEADER, function (string|null $html) use ($page): string|null {
                    if (get_class($page) != Page::class) {
                        return $html;
                    }

                    $schema = [
                        '@context' => 'https://schema.org',
                        '@type' => 'Organization',
                        'name' => theme_option('site_title'),
                        'url' => $page->url,
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => RvMedia::getImageUrl(theme_option('logo')),
                        ],
                    ];

                    return $html . Html::tag('script', json_encode($schema), ['type' => 'application/ld+json'])
                            ->toHtml();
                }, 2);
            }, 2, 2);
        }

        add_filter(PAGE_FILTER_FRONT_PAGE_CONTENT, fn (string|null $html) => (string) $html, 1, 2);
    }

    public function addThemeOptions(): void
    {
        $pages = Page::query()
            ->where('status', BaseStatusEnum::PUBLISHED);

        $pages = RepositoryHelper::applyBeforeExecuteQuery($pages, new Page())
            ->pluck('name', 'id')
            ->all();

        theme_option()
            ->setSection([
                'title' => 'Page',
                'desc' => 'Theme options for Page',
                'id' => 'opt-text-subsection-page',
                'subsection' => true,
                'icon' => 'fa fa-book',
                'fields' => [
                    [
                        'id' => 'homepage_id',
                        'type' => 'customSelect',
                        'label' => trans('packages/page::pages.settings.show_on_front'),
                        'attributes' => [
                            'name' => 'homepage_id',
                            'list' => ['' => trans('packages/page::pages.settings.select')] + $pages,
                            'value' => '',
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function registerMenuOptions(): void
    {
        if (Auth::user()->hasPermission('pages.index')) {
            Menu::registerMenuOptions(Page::class, trans('packages/page::pages.menu'));
        }
    }

    public function addPageStatsWidget(array $widgets, Collection $widgetSettings): array
    {
        $pages = Page::query()->where('status', BaseStatusEnum::PUBLISHED)->count();

        return (new DashboardWidgetInstance())
            ->setType('stats')
            ->setPermission('pages.index')
            ->setTitle(trans('packages/page::pages.pages'))
            ->setKey('widget_total_pages')
            ->setIcon('far fa-file-alt')
            ->setColor('#32c5d2')
            ->setStatsTotal($pages)
            ->setRoute(route('pages.index'))
            ->init($widgets, $widgetSettings);
    }

    public function handleSingleView(Slug|array $slug): Slug|array
    {
        return (new PageService())->handleFrontRoutes($slug);
    }
}
