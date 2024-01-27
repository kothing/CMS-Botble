<?php

use Botble\ACL\Models\User;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Kris\LaravelFormBuilder\FormHelper;
use Theme\Stories\Fields\ThemeIconField;

register_page_template([
    'full-width' => __('Full width'),
    'homepage' => __('Homepage'),
    'right-sidebar' => __('Right sidebar'),
]);

register_sidebar([
    'id' => 'footer_sidebar',
    'name' => __('Footer sidebar'),
    'description' => __('Sidebar in the footer of page'),
]);

Menu::addMenuLocation('header-menu', __('Header Navigation'));

RvMedia::setUploadPathAndURLToPublic();

if (is_plugin_active('blog')) {
    add_action(BASE_ACTION_META_BOXES, function ($context, $object) {
        switch (get_class($object)) {
            case Post::class:
                if ($context == 'top') {
                    MetaBox::addMetaBox(
                        'additional_post_fields',
                        __('Addition Information'),
                        function () {
                            $timeToRead = null;
                            $layout = null;
                            $args = func_get_args();
                            if (! empty($args[0])) {
                                $timeToRead = MetaBox::getMetaData($args[0], 'time_to_read', true);
                                $layout = MetaBox::getMetaData($args[0], 'layout', true);
                            }

                            if (! $layout && theme_option('blog_single_layout')) {
                                $layout = theme_option('blog_single_layout');
                            }

                            return Theme::partial('blog-post-fields', compact('timeToRead', 'layout'));
                        },
                        get_class($object),
                        $context
                    );
                }

                break;
            case Category::class:
                if ($context == 'side') {
                    MetaBox::addMetaBox('additional_blog_category_fields', __('Addition Information'), function () {
                        $image = null;
                        $args = func_get_args();
                        if (! empty($args[0])) {
                            $image = MetaBox::getMetaData($args[0], 'image', true);
                        }

                        return Theme::partial('blog-category-fields', compact('image'));
                    }, get_class($object), $context);
                }

                break;
        }
    }, 30, 3);

    add_action([BASE_ACTION_AFTER_CREATE_CONTENT, BASE_ACTION_AFTER_UPDATE_CONTENT], function ($type, $request, $object) {
        switch (get_class($object)) {
            case Post::class:
                if ($request->has('time_to_read')) {
                    MetaBox::saveMetaBoxData($object, 'time_to_read', $request->input('time_to_read'));
                }

                if ($request->has('layout')) {
                    MetaBox::saveMetaBoxData($object, 'layout', $request->input('layout'));
                }

                break;
            case Category::class:
                if ($request->has('image')) {
                    MetaBox::saveMetaBoxData($object, 'image', $request->input('image'));
                }

                break;
        }
    }, 230, 3);
}

app()->booted(function () {
    if (is_plugin_active('blog')) {
        Category::resolveRelationUsing('image', function ($model) {
            return $model->morphOne(MetaBoxModel::class, 'reference')->where('meta_key', 'image');
        });
    }
});

if (is_plugin_active('ads')) {
    AdsManager::registerLocation('panel-ads', __('Panel Ads'))
        ->registerLocation('top-sidebar-ads', __('Top Sidebar Ads'))
        ->registerLocation('bottom-sidebar-ads', __('Bottom Sidebar Ads'));
}

Form::component('themeIcon', Theme::getThemeNamespace() . '::partials.icons-field', [
    'name',
    'value' => null,
    'attributes' => [],
]);

add_filter('form_custom_fields', function (FormAbstract $form, FormHelper $formHelper) {
    if (! $formHelper->hasCustomField('themeIcon')) {
        $form->addCustomField('themeIcon', ThemeIconField::class);
    }

    return $form;
}, 29, 2);

add_filter(BASE_FILTER_BEFORE_RENDER_FORM, function ($form, $data) {
    if (get_class($data) == User::class && $form->getFormOption('id') == 'profile-form') {
        $form
            ->add('bio', 'editor', [
                'label' => __('Bio (Write something about yourself...)'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => __('Write something about yourself...'),
                ],
                'wrapper' => [
                    'class' => $form->getFormHelper()->getConfig('defaults.wrapper_class') . ' col-md-12',
                ],
                'value' => MetaBox::getMetaData($data, 'bio', true),
            ]);
    }

    return $form;
}, 127, 2);

add_action(USER_ACTION_AFTER_UPDATE_PROFILE, function ($screen, $request, $user) {
    if ($screen == USER_MODULE_SCREEN_NAME && $request->has('bio')) {
        MetaBox::saveMetaBoxData($user, 'bio', $request->input('bio'));
    }
}, 127, 3);

if (! function_exists('random_color')) {
    function random_color(): string
    {
        $colors = ['warning', 'primary', 'info', 'success'];

        return 'text-' . $colors[array_rand($colors)];
    }
}

if (! function_exists('get_time_to_read')) {
    function get_time_to_read(Post $post): string
    {
        $timeToRead = MetaBox::getMetaData($post, 'time_to_read', true);

        if ($timeToRead) {
            return number_format($timeToRead);
        }

        return number_format(strlen(strip_tags($post->content)) / 300);
    }
}

if (! function_exists('get_blog_single_layouts')) {
    function get_blog_single_layouts(): array
    {
        return [
            '' => __('Inherit'),
            'blog-right-sidebar' => __('Blog Right Sidebar'),
            'blog-left-sidebar' => __('Blog Left Sidebar'),
            'blog-full-width' => __('Full width'),
        ];
    }
}

if (! function_exists('get_blog_layouts')) {
    function get_blog_layouts(): array
    {
        return [
            'grid' => __('Grid layout'),
            'list' => __('List layout'),
            'big' => __('Big layout'),
        ];
    }
}

if (! function_exists('display_ad')) {
    function display_ad(string $location, array $attributes = []): string
    {
        if (! is_plugin_active('ads')) {
            return '';
        }

        return AdsManager::display($location, $attributes);
    }
}
