<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Blog\Repositories\Interfaces\CategoryInterface;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Supports\ThemeSupport;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

app()->booted(function () {
    ThemeSupport::registerGoogleMapsShortcode();
    ThemeSupport::registerYoutubeShortcode();

    if (is_plugin_active('blog')) {
        Shortcode::register('featured-posts', __('Featured posts'), __('Featured posts'), function (ShortcodeCompiler $shortcode) {
            $posts = get_featured_posts((int) $shortcode->limit ?: 5, [
                'author',
                'categories' => function ($query) {
                    $query->limit(1);
                },
            ]);

            return Theme::partial('shortcodes.featured-posts', compact('posts'));
        });

        Shortcode::setAdminConfig('featured-posts', function (array $attributes, string|null $content) {
            return Theme::partial('shortcodes.featured-posts-admin-config', compact('attributes', 'content'));
        });

        Shortcode::register('recent-posts', __('Recent posts'), __('Recent posts'), function (ShortcodeCompiler $shortcode) {
            $posts = get_latest_posts(7, [], ['slugable']);

            return Theme::partial('shortcodes.recent-posts', ['title' => $shortcode->title, 'posts' => $posts]);
        });

        Shortcode::setAdminConfig('recent-posts', function (array $attributes, string|null $content) {
            return Theme::partial('shortcodes.recent-posts-admin-config', compact('attributes', 'content'));
        });

        Shortcode::register(
            'featured-categories-posts',
            __('Featured categories posts'),
            __('Featured categories posts'),
            function (ShortcodeCompiler $shortcode) {
                $with = [
                    'slugable',
                    'posts' => function (BelongsToMany $query) {
                        $query
                            ->where('status', BaseStatusEnum::PUBLISHED)
                            ->orderBy('created_at', 'DESC');
                    },
                    'posts.slugable',
                ];

                if (is_plugin_active('language-advanced')) {
                    $with[] = 'posts.translations';
                }

                $posts = collect();

                if ($shortcode->category_id) {
                    $with['posts'] = function (BelongsToMany $query) {
                        $query
                            ->where('status', BaseStatusEnum::PUBLISHED)
                            ->orderBy('created_at', 'DESC')
                            ->take(6);
                    };

                    $category = app(CategoryInterface::class)
                        ->getModel()
                        ->with($with)
                        ->where([
                            'status' => BaseStatusEnum::PUBLISHED,
                            'id' => $shortcode->category_id,
                        ])
                        ->select([
                            'id',
                            'name',
                            'description',
                            'icon',
                        ])
                        ->first();

                    if ($category) {
                        $posts = $category->posts;
                    } else {
                        $posts = collect();
                    }
                } else {
                    $categories = get_featured_categories(2, $with);

                    foreach ($categories as $category) {
                        $posts = $posts->merge($category->posts->take(3));
                    }

                    $posts = $posts->sortByDesc('created_at');
                }

                return Theme::partial(
                    'shortcodes.featured-categories-posts',
                    ['title' => $shortcode->title, 'posts' => $posts]
                );
            }
        );

        Shortcode::setAdminConfig('featured-categories-posts', function (array $attributes) {
            $categories = app(CategoryInterface::class)->pluck('name', 'id', ['status' => BaseStatusEnum::PUBLISHED]);

            return Theme::partial(
                'shortcodes.featured-categories-posts-admin-config',
                compact('attributes', 'categories')
            );
        });
    }

    if (is_plugin_active('gallery')) {
        Shortcode::register('all-galleries', __('All Galleries'), __('All Galleries'), function (ShortcodeCompiler $shortcode) {
            return Theme::partial('shortcodes.all-galleries', ['limit' => (int)$shortcode->limit]);
        });

        Shortcode::setAdminConfig('all-galleries', function (array $attributes, string|null $content) {
            return Theme::partial('shortcodes.all-galleries-admin-config', compact('attributes', 'content'));
        });
    }
});
