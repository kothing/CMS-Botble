<?php

use Botble\Ads\Repositories\Interfaces\AdsInterface;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Blog\Repositories\Interfaces\CategoryInterface;
use Botble\Theme\Supports\ThemeSupport;

app()->booted(function () {
    ThemeSupport::registerGoogleMapsShortcode();
    ThemeSupport::registerYoutubeShortcode();

    if (is_plugin_active('blog')) {
        add_shortcode(
            'featured-categories',
            __('Featured categories'),
            __('Add featured categories'),
            function ($shortcode) {
                return Theme::partial('short-codes.featured-categories', ['title' => $shortcode->title, 'shortcode' => $shortcode]);
            }
        );

        shortcode()->setAdminConfig('featured-categories', function ($attributes) {
            return Theme::partial('short-codes.featured-categories-admin-config', compact('attributes'));
        });

        add_shortcode('featured-posts', __('Featured posts'), __('Add featured posts'), function ($shortcode) {
            return Theme::partial('short-codes.featured-posts', [
                'title' => $shortcode->title,
                'limit' => $shortcode->limit,
            ]);
        });

        shortcode()->setAdminConfig('featured-posts', function ($attributes) {
            return Theme::partial('short-codes.featured-posts-admin-config', compact('attributes'));
        });

        add_shortcode(
            'blog-categories-posts',
            __('Blog categories posts'),
            __('Blog categories posts'),
            function ($shortcode) {
                $category = app(CategoryInterface::class)
                    ->findById($shortcode->category_id, [
                        'slugable',
                        'posts' => function ($query) {
                            $query
                                ->latest()
                                ->with(['slugable', 'categories', 'categories.slugable'])
                                ->where('status', BaseStatusEnum::PUBLISHED)
                                ->limit(4);
                        },
                    ]);

                if (! $category) {
                    return null;
                }

                return Theme::partial('short-codes.blog-categories-posts', compact('category'));
            }
        );

        shortcode()->setAdminConfig('blog-categories-posts', function ($attributes) {
            $categories = app(CategoryInterface::class)->allBy(['status' => BaseStatusEnum::PUBLISHED]);

            return Theme::partial('short-codes.blog-categories-posts-admin-config', compact('categories', 'attributes'));
        });

        add_shortcode(
            'categories-with-posts',
            __('Categories with Posts'),
            __('Categories with Posts'),
            function ($shortcode) {
                $attributes = $shortcode->toArray();

                $categories = collect([]);

                for ($i = 1; $i <= 3; $i++) {
                    if (! Arr::has($attributes, 'category_id_' . $i)) {
                        continue;
                    }

                    $category = app(CategoryInterface::class)->advancedGet([
                        'condition' => ['categories.id' => Arr::get($attributes, 'category_id_' . $i)],
                        'take' => 1,
                        'with' => [
                            'slugable',
                            'posts' => function ($query) {
                                return $query
                                    ->latest()
                                    ->with(['slugable'])
                                    ->where('status', BaseStatusEnum::PUBLISHED)
                                    ->limit(3);
                            },
                        ],
                    ]);

                    if ($category) {
                        $categories[] = $category;
                    }
                }

                return Theme::partial('short-codes.categories-with-posts', compact('categories'));
            }
        );

        shortcode()->setAdminConfig('categories-with-posts', function ($attributes) {
            $categories = app(CategoryInterface::class)->allBy(['status' => BaseStatusEnum::PUBLISHED]);

            return Theme::partial('short-codes.categories-with-posts-admin-config', compact('categories', 'attributes'));
        });

        add_shortcode('featured-posts-slider', __('Featured posts slider'), __('Featured posts slider'), function ($shortcode) {
            return Theme::partial('short-codes.featured-posts-slider', ['limit' => $shortcode->limit]);
        });

        shortcode()->setAdminConfig('featured-posts-slider', function ($attributes) {
            return Theme::partial('short-codes.featured-posts-slider-admin-config', compact('attributes'));
        });

        add_shortcode(
            'featured-posts-slider-full',
            __('Featured posts slider full'),
            __('Featured posts slider full'),
            function ($shortcode) {
                return Theme::partial('short-codes.featured-posts-slider-full', ['limit' => $shortcode->limit]);
            }
        );

        shortcode()->setAdminConfig('featured-posts-slider-full', function ($attributes) {
            return Theme::partial('short-codes.featured-posts-slider-full-admin-config', compact('attributes'));
        });

        add_shortcode('blog-list', __('Blog list'), __('Add blog posts list'), function ($shortcode) {
            $limit = $shortcode->limit ?: 12;

            $posts = get_all_posts(true, $limit);

            return Theme::partial('short-codes.blog-list', compact('posts'));
        });

        shortcode()->setAdminConfig('blog-list', function ($attributes) {
            return Theme::partial('short-codes.blog-list-admin-config', compact('attributes'));
        });

        add_shortcode('blog-big', __('Blog big'), __('Add blog posts big'), function ($shortcode) {
            $limit = $shortcode->limit ?: 12;

            $posts = get_all_posts(true, $limit);

            return Theme::partial('short-codes.blog-big', compact('posts'));
        });

        shortcode()->setAdminConfig('blog-big', function ($attributes) {
            return Theme::partial('short-codes.blog-big-admin-config', compact('attributes'));
        });
    }

    add_shortcode('about-banner', __('About banner'), __('About banner'), function ($shortcode) {
        return Theme::partial('short-codes.about-banner', [
            'title' => $shortcode->title,
            'subtitle' => $shortcode->subtitle,
            'textMuted' => $shortcode->text_muted,
            'newsletterTitle' => $shortcode->newsletter_title,
            'image' => $shortcode->image,
            'showNewsletterForm' => $shortcode->show_newsletter_form,
        ]);
    });

    shortcode()->setAdminConfig('about-banner', function ($attributes) {
        return Theme::partial('short-codes.about-banner-admin-config', compact('attributes'));
    });

    if (is_plugin_active('ads')) {
        add_shortcode('theme-ads', __('Theme ads'), __('Theme ads'), function ($shortcode) {
            $ads = [];
            $attributes = $shortcode->toArray();

            for ($i = 1; $i < 5; $i++) {
                if (isset($attributes['key_' . $i]) && ! empty($attributes['key_' . $i])) {
                    $ad = AdsManager::displayAds((string)$attributes['key_' . $i]);
                    if ($ad) {
                        $ads[] = $ad;
                    }
                }
            }

            $ads = array_filter($ads);

            return Theme::partial('short-codes.theme-ads', compact('ads'));
        });

        shortcode()->setAdminConfig('theme-ads', function ($attributes) {
            $ads = app(AdsInterface::class)->getModel()
                ->where('status', BaseStatusEnum::PUBLISHED)
                ->notExpired()
                ->get();

            return Theme::partial('short-codes.theme-ads-admin-config', compact('ads', 'attributes'));
        });
    }

    if (is_plugin_active('gallery')) {
        add_filter('galleries_box_template_view', function () {
            return Theme::getThemeNamespace() . '::partials.short-codes.galleries';
        }, 120);

        shortcode()->setAdminConfig('gallery', function ($attributes) {
            return Theme::partial('short-codes.galleries-admin-config', compact('attributes'));
        });
    }
});
