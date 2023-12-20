<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Api\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'settings/api'], function () {
            Route::get('', [
                'as' => 'api.settings',
                'uses' => 'ApiController@settings',
            ]);

            Route::post('', [
                'as' => 'api.settings.update',
                'uses' => 'ApiController@storeSettings',
                'permission' => 'api.settings',
            ]);
        });
    });
});
