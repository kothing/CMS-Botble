<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\RequestLog\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'request-logs', 'as' => 'request-log.'], function () {
            Route::resource('', 'RequestLogController')
                ->only(['index', 'destroy'])->parameters(['' => 'request-log']);

            Route::get('widgets/request-errors', [
                'as' => 'widget.request-errors',
                'uses' => 'RequestLogController@getWidgetRequestErrors',
                'permission' => 'request-log.index',
            ]);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'RequestLogController@deletes',
                'permission' => 'request-log.destroy',
            ]);

            Route::get('items/empty', [
                'as' => 'empty',
                'uses' => 'RequestLogController@deleteAll',
                'permission' => 'request-log.destroy',
            ]);
        });
    });
});
