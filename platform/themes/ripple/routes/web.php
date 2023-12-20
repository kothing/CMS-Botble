<?php

use Illuminate\Support\Facades\Route;
use Theme\Ripple\Http\Controllers\RippleController;

Route::group(['controller' => RippleController::class, 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        // Add your custom route here
        // Ex: Route::get('hello', 'getHello');

        Route::get('ajax/search', 'getSearch')->name('public.ajax.search');
    });
});

Theme::routes();
