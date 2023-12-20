<?php

namespace Botble\Gallery\Providers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\Html;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Supports\ServiceProvider;
use Botble\Gallery\Facades\Gallery;
use Botble\Gallery\Repositories\Interfaces\GalleryInterface;
use Botble\Gallery\Services\GalleryService;
use Botble\Page\Models\Page;
use Botble\Page\Repositories\Interfaces\PageInterface;
use Botble\Shortcode\Compilers\Shortcode;
use Botble\Slug\Models\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'addGalleryBox'], 13, 2);

        if (function_exists('shortcode')) {
            add_shortcode(
                'gallery',
                trans('plugins/gallery::gallery.gallery_images'),
                trans('plugins/gallery::gallery.add_gallery_short_code'),
                [$this, 'render']
            );

            shortcode()->setAdminConfig('gallery', function ($attributes, $content) {
                return view('plugins/gallery::shortcodes.gallery-admin-config', compact('attributes', 'content'))
                    ->render();
            });
        }

        add_filter(BASE_FILTER_PUBLIC_SINGLE_DATA, [$this, 'handleSingleView'], 11);

        if (defined('PAGE_MODULE_SCREEN_NAME')) {
            add_filter(PAGE_FILTER_PAGE_NAME_IN_ADMIN_LIST, [$this, 'addAdditionNameToPageName'], 11, 2);
        }

        if (function_exists('theme_option')) {
            add_action(RENDERING_THEME_OPTIONS_PAGE, [$this, 'addThemeOptions'], 11);
        }
    }

    public function addGalleryBox(string $context, ?Model $object): void
    {
        if ($object && in_array(get_class($object), Gallery::getSupportedModules()) && $context == 'advanced') {
            Assets::addStylesDirectly(['vendor/core/plugins/gallery/css/admin-gallery.css'])
                ->addScriptsDirectly(['vendor/core/plugins/gallery/js/gallery-admin.js'])
                ->addScripts(['sortable']);

            MetaBox::addMetaBox(
                'gallery_wrap',
                trans('plugins/gallery::gallery.gallery_box'),
                [$this, 'galleryMetaField'],
                get_class($object),
                $context
            );
        }
    }

    public function galleryMetaField(): string
    {
        $value = null;
        $args = func_get_args();

        if ($args[0] && $args[0]->id) {
            $value = gallery_meta_data($args[0]);
        }

        return view('plugins/gallery::gallery-box', compact('value'))->render();
    }

    public function render(Shortcode $shortcode): string
    {
        Gallery::registerAssets();

        $limit = (int)$shortcode->limit;

        $galleries = app(GalleryInterface::class)->getAll(limit: max($limit, 0));

        $view = apply_filters('galleries_box_template_view', 'plugins/gallery::shortcodes.gallery');

        return view($view, compact('shortcode', 'galleries'))->render();
    }

    public function handleSingleView(Slug|array $slug): Slug|array
    {
        return (new GalleryService())->handleFrontRoutes($slug);
    }

    public function addAdditionNameToPageName(string|null $name, Page $page): string|null
    {
        if ($page->id == theme_option('galleries_page_id')) {
            $subTitle = Html::tag(
                'span',
                trans('plugins/gallery::gallery.galleries_page'),
                ['class' => 'additional-page-name']
            )
                ->toHtml();

            if (Str::contains($name, ' —')) {
                return $name . ', ' . $subTitle;
            }

            return $name . ' —' . $subTitle;
        }

        return $name;
    }

    public function addThemeOptions()
    {
        $pages = $this->app->make(PageInterface::class)->pluck('name', 'id', ['status' => BaseStatusEnum::PUBLISHED]);

        theme_option()
            ->setField([

                'id' => 'galleries_page_id',
                'section_id' => 'opt-text-subsection-page',
                'type' => 'customSelect',
                'label' => trans('plugins/gallery::gallery.galleries_page'),
                'attributes' => [
                    'name' => 'galleries_page_id',
                    'list' => ['' => trans('packages/page::pages.settings.select')] + $pages,
                    'value' => '',
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ]);
    }
}
