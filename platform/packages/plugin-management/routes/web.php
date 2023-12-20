<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\PluginManagement\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'plugins'], function () {
            Route::get('', [
                'as' => 'plugins.index',
                'uses' => 'PluginManagementController@index',
            ]);

            Route::put('status', [
                'as' => 'plugins.change.status',
                'uses' => 'PluginManagementController@update',
                'middleware' => 'preventDemo',
                'permission' => 'plugins.index',
            ]);

            Route::delete('{plugin}', [
                'as' => 'plugins.remove',
                'uses' => 'PluginManagementController@destroy',
                'middleware' => 'preventDemo',
                'permission' => 'plugins.index',
            ]);

            Route::post('check-requirement', [
                'as' => 'plugins.check-requirement',
                'uses' => 'PluginManagementController@checkRequirement',
                'permission' => 'plugins.index',
            ]);
        });

        Route::group(['prefix' => 'plugins/marketplace', 'permission' => 'plugins.marketplace'], function () {
            Route::get('', [
                'as' => 'plugins.marketplace',
                'uses' => 'MarketplaceController@index',
            ]);

            Route::group(['prefix' => 'ajax', 'as' => 'plugins.marketplace.ajax.'], function () {
                Route::get('plugins', [
                    'as' => 'list',
                    'uses' => 'MarketplaceController@list',
                ]);

                Route::get('{id}', [
                    'as' => 'detail',
                    'uses' => 'MarketplaceController@detail',
                ]);

                Route::get('{id}/iframe', [
                    'as' => 'iframe',
                    'uses' => 'MarketplaceController@iframe',
                ]);

                Route::post('{id}/install', [
                    'as' => 'install',
                    'uses' => 'MarketplaceController@install',
                    'middleware' => 'preventDemo',
                ]);

                Route::post('{id}/update/{name?}', [
                    'as' => 'update',
                    'uses' => 'MarketplaceController@update',
                    'middleware' => 'preventDemo',
                ]);

                Route::post('check-update', [
                    'as' => 'check-update',
                    'uses' => 'MarketplaceController@checkUpdate',
                ]);
            });
        });
    });
});
