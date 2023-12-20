<?php

use Botble\Base\Facades\BaseHelper;
use Botble\Page\Models\Page;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Page\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'pages', 'as' => 'pages.'], function () {
            Route::resource('', 'PageController')->parameters(['' => 'page']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'PageController@deletes',
                'permission' => 'pages.destroy',
            ]);
        });
    });

    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
            if (SlugHelper::getPrefix(Page::class)) {
                Route::get(SlugHelper::getPrefix(Page::class) . '/{slug}', [
                    'uses' => 'PublicController@getPage',
                ]);
            }
        });
    }
});
