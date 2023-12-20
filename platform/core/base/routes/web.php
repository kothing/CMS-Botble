<?php

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Base\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'system/info'], function () {
            Route::match(['GET', 'POST'], '', [
                'as' => 'system.info',
                'uses' => 'SystemController@getInfo',
                'permission' => 'superuser',
            ]);
        });

        Route::group(['prefix' => 'system/cache'], function () {
            Route::get('', [
                'as' => 'system.cache',
                'uses' => 'SystemController@getCacheManagement',
                'permission' => 'superuser',
            ]);

            Route::post('clear', [
                'as' => 'system.cache.clear',
                'uses' => 'SystemController@postClearCache',
                'permission' => 'superuser',
                'middleware' => 'preventDemo',
            ]);
        });

        Route::post('membership/authorize', [
            'as' => 'membership.authorize',
            'uses' => 'SystemController@authorize',
            'permission' => false,
        ]);

        Route::get('menu-items-count', [
            'as' => 'menu-items-count',
            'uses' => 'SystemController@getMenuItemsCount',
            'permission' => false,
        ]);

        Route::get('system/check-update', [
            'as' => 'system.check-update',
            'uses' => 'SystemController@getCheckUpdate',
            'permission' => 'superuser',
        ]);

        Route::get('system/updater', [
            'as' => 'system.updater',
            'uses' => 'SystemController@getUpdater',
            'permission' => 'superuser',
        ]);

        Route::post('system/updater', [
            'as' => 'system.updater.post',
            'uses' => 'SystemController@postUpdater',
            'permission' => 'superuser',
            'middleware' => 'preventDemo',
        ]);

        Route::get('system/cleanup', [
            'as' => 'system.cleanup',
            'uses' => 'SystemController@getCleanup',
            'permission' => 'superuser',
        ]);

        Route::post('system/cleanup', [
            'as' => 'system.cleanup.process',
            'uses' => 'SystemController@getCleanup',
            'permission' => 'superuser',
            'middleware' => 'preventDemo',
        ]);

        Route::group(['prefix' => 'notifications', 'as' => 'notifications.', 'permission' => false], function () {
            Route::get('get-notifications', [
                'as' => 'get-notification',
                'uses' => 'NotificationController@getNotification',
            ]);

            Route::delete('destroy-notification/{id}', [
                'as' => 'destroy-notification',
                'uses' => 'NotificationController@delete',
            ])->wherePrimaryKey();

            Route::get('read-notification/{id}', [
                'as' => 'read-notification',
                'uses' => 'NotificationController@read',
            ])->wherePrimaryKey();

            Route::put('read-all-notification', [
                'as' => 'read-all-notification',
                'uses' => 'NotificationController@readAll',
            ]);

            Route::delete('destroy-all-notification', [
                'as' => 'destroy-all-notification',
                'uses' => 'NotificationController@deleteAll',
            ]);

            Route::get('update-notifications-count', [
                'as' => 'update-notifications-count',
                'uses' => 'NotificationController@countNotification',
            ]);
        });
    });

    Route::get('settings-language/{alias}', [SystemController::class, 'getLanguage'])->name('settings.language');
});
