<?php

namespace Botble\CustomField\Providers;

use Botble\ACL\Repositories\Interfaces\RoleInterface;
use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\CustomField\Facades\CustomField as CustomFieldFacade;
use Botble\CustomField\Models\CustomField;
use Botble\CustomField\Models\FieldGroup;
use Botble\CustomField\Models\FieldItem;
use Botble\CustomField\Repositories\Eloquent\CustomFieldRepository;
use Botble\CustomField\Repositories\Eloquent\FieldGroupRepository;
use Botble\CustomField\Repositories\Eloquent\FieldItemRepository;
use Botble\CustomField\Repositories\Interfaces\CustomFieldInterface;
use Botble\CustomField\Repositories\Interfaces\FieldGroupInterface;
use Botble\CustomField\Repositories\Interfaces\FieldItemInterface;
use Botble\CustomField\Support\CustomFieldSupport;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Page\Models\Page;
use Botble\Page\Repositories\Interfaces\PageInterface;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Events\RouteMatched;

class CustomFieldServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        AliasLoader::getInstance()->alias('CustomField', CustomFieldFacade::class);

        $this->app->bind(CustomFieldInterface::class, function () {
            return new CustomFieldRepository(new CustomField());
        });

        $this->app->bind(FieldGroupInterface::class, function () {
            return new FieldGroupRepository(new FieldGroup());
        });

        $this->app->bind(FieldItemInterface::class, function () {
            return new FieldItemRepository(new FieldItem());
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/custom-field')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions', 'general'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadMigrations()
            ->publishAssets();

        $this->app->register(EventServiceProvider::class);

        $this->app['events']->listen(RouteMatched::class, function () {
            DashboardMenu::registerItem([
                'id' => 'cms-plugins-custom-field',
                'priority' => 5,
                'parent_id' => null,
                'name' => 'plugins/custom-field::base.admin_menu.title',
                'icon' => 'fas fa-cubes',
                'url' => route('custom-fields.index'),
                'permissions' => ['custom-fields.index'],
            ]);

            $this->registerUsersFields();
            $this->registerPagesFields();

            if (is_plugin_active('blog')) {
                $this->registerBlogFields();
            }
        });

        if (defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
            LanguageAdvancedManager::registerModule(CustomField::class, [
                'value',
            ]);
        }

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }

    protected function registerUsersFields(): CustomFieldSupport
    {
        return CustomFieldFacade::registerRule(
            'other',
            trans('plugins/custom-field::rules.logged_in_user'),
            'logged_in_user',
            function () {
                $users = $this->app->make(UserInterface::class)->all();
                $userArr = [];
                foreach ($users as $user) {
                    $userArr[$user->id] = $user->username . ' - ' . $user->email;
                }

                return $userArr;
            }
        )
            ->registerRule(
                'other',
                trans('plugins/custom-field::rules.logged_in_user_has_role'),
                'logged_in_user_has_role',
                function () {
                    $roles = $this->app->make(RoleInterface::class)->all();
                    $rolesArr = [];
                    foreach ($roles as $role) {
                        $rolesArr[$role->slug] = $role->name . ' - (' . $role->slug . ')';
                    }

                    return $rolesArr;
                }
            );
    }

    protected function registerPagesFields(): bool|CustomFieldSupport
    {
        if (! defined('PAGE_MODULE_SCREEN_NAME')) {
            return false;
        }

        return CustomFieldFacade::registerRule(
            'basic',
            trans('plugins/custom-field::rules.page_template'),
            'page_template',
            function () {
                return get_page_templates();
            }
        )
            ->registerRule('basic', trans('plugins/custom-field::rules.page'), Page::class, function () {
                return $this->app->make(PageInterface::class)
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
                    Page::class => trans('plugins/custom-field::rules.model_name_page'),
                ];
            });
    }

    protected function registerBlogFields(): bool|CustomFieldSupport
    {
        if (! defined('POST_MODULE_SCREEN_NAME')) {
            return false;
        }

        return CustomFieldFacade::registerRuleGroup('blog')
            ->registerRule('blog', trans('plugins/custom-field::rules.category'), Category::class, function () {
                return $this->getBlogCategoryIds();
            })
            ->registerRule(
                'blog',
                trans('plugins/custom-field::rules.post_with_related_category'),
                Post::class . '_post_with_related_category',
                function () {
                    return $this->getBlogCategoryIds();
                }
            )
            ->registerRule(
                'blog',
                trans('plugins/custom-field::rules.post_format'),
                Post::class . '_post_format',
                function () {
                    $formats = [];
                    foreach (get_post_formats() as $key => $format) {
                        $formats[$key] = $format['name'];
                    }

                    return $formats;
                }
            )
            ->expandRule('other', trans('plugins/custom-field::rules.model_name'), 'model_name', function () {
                return [
                    Post::class => trans('plugins/custom-field::rules.model_name_post'),
                    Category::class => trans('plugins/custom-field::rules.model_name_category'),
                ];
            });
    }

    protected function getBlogCategoryIds(): array
    {
        $categories = get_categories();

        $categoriesArr = [];
        foreach ($categories as $row) {
            $categoriesArr[$row->id] = $row->indent_text . ' ' . $row->name;
        }

        return $categoriesArr;
    }
}
