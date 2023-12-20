<?php

use Botble\Analytics\Http\Controllers\AnalyticsController;
use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => BaseHelper::getAdminPrefix() . '/analytics',
        'controller' => AnalyticsController::class,
        'middleware' => ['web', 'core', 'auth'],
        'as' => 'analytics.',
    ],
    function () {
        Route::get('general', [
            'as' => 'general',
            'uses' => 'getGeneral',
        ]);

        Route::get('page', [
            'as' => 'page',
            'uses' => 'getTopVisitPages',
        ]);

        Route::get('browser', [
            'as' => 'browser',
            'uses' => 'getTopBrowser',
        ]);

        Route::get('referrer', [
            'as' => 'referrer',
            'uses' => 'getTopReferrer',
        ]);
    }
);
