<?php

use Botble\Base\Facades\BaseHelper;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Tag;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Blog\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix() . '/blog', 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'posts', 'as' => 'posts.'], function () {
            Route::resource('', 'PostController')
                ->parameters(['' => 'post']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'PostController@deletes',
                'permission' => 'posts.destroy',
            ]);

            Route::get('widgets/recent-posts', [
                'as' => 'widget.recent-posts',
                'uses' => 'PostController@getWidgetRecentPosts',
                'permission' => 'posts.index',
            ]);
        });

        Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
            Route::resource('', 'CategoryController')
                ->parameters(['' => 'category']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'CategoryController@deletes',
                'permission' => 'categories.destroy',
            ]);
        });

        Route::group(['prefix' => 'tags', 'as' => 'tags.'], function () {
            Route::resource('', 'TagController')
                ->parameters(['' => 'tag']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'TagController@deletes',
                'permission' => 'tags.destroy',
            ]);

            Route::get('all', [
                'as' => 'all',
                'uses' => 'TagController@getAllTags',
                'permission' => 'tags.index',
            ]);
        });
    });

    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
            Route::get('search', [
                'as' => 'public.search',
                'uses' => 'PublicController@getSearch',
            ]);

            if (SlugHelper::getPrefix(Tag::class, 'tag')) {
                Route::get(SlugHelper::getPrefix(Tag::class, 'tag') . '/{slug}', [
                    'as' => 'public.tag',
                    'uses' => 'PublicController@getTag',
                ]);
            }

            if (SlugHelper::getPrefix(Post::class)) {
                Route::get(SlugHelper::getPrefix(Post::class) . '/{slug}', [
                    'uses' => 'PublicController@getPost',
                ]);
            }

            if (SlugHelper::getPrefix(Category::class)) {
                Route::get(SlugHelper::getPrefix(Category::class) . '/{slug}', [
                    'uses' => 'PublicController@getCategory',
                ]);
            }
        });
    }
});
