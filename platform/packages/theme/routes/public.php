<?php

use Botble\Theme\Events\ThemeRoutingAfterEvent;
use Botble\Theme\Events\ThemeRoutingBeforeEvent;
use Botble\Theme\Facades\SiteMapManager;
use Botble\Theme\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::group(['controller' => PublicController::class, 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        event(new ThemeRoutingBeforeEvent(app()->make('router')));

        Route::get('/', [
            'as' => 'public.index',
            'uses' => 'getIndex',
        ]);

        Route::get('{key}.{extension}', 'getSiteMapIndex')
            ->where('key', '^' . collect(SiteMapManager::getKeys())->map(fn ($item) => '(?:' . $item . ')')->implode('|') . '$')
            ->whereIn('extension', SiteMapManager::allowedExtensions())
            ->name('public.sitemap.index');

        Route::get('{slug?}', [
            'as' => 'public.single',
            'uses' => 'getView',
        ]);

        event(new ThemeRoutingAfterEvent(app()->make('router')));
    });
});
