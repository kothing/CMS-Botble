<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Media\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'media', 'as' => 'media.', 'permission' => 'media.index'], function () {
            Route::get('', [
                'as' => 'index',
                'uses' => 'MediaController@getMedia',
            ]);

            Route::get('popup', [
                'as' => 'popup',
                'uses' => 'MediaController@getPopup',
            ]);

            Route::get('list', [
                'as' => 'list',
                'uses' => 'MediaController@getList',
            ]);

            Route::get('breadcrumbs', [
                'as' => 'breadcrumbs',
                'uses' => 'MediaController@getBreadcrumbs',
            ]);

            Route::post('global-actions', [
                'as' => 'global_actions',
                'uses' => 'MediaController@postGlobalActions',
            ]);

            Route::post('download', [
                'as' => 'download',
                'uses' => 'MediaController@download',
            ]);

            Route::group(['prefix' => 'files'], function () {
                Route::post('upload', [
                    'as' => 'files.upload',
                    'uses' => 'MediaFileController@postUpload',
                ]);

                Route::post('upload-from-editor', [
                    'as' => 'files.upload.from.editor',
                    'uses' => 'MediaFileController@postUploadFromEditor',
                ]);

                Route::post('download-url', [
                    'as' => 'download_url',
                    'uses' => 'MediaFileController@postDownloadUrl',
                ]);
            });

            Route::group(['prefix' => 'folders'], function () {
                Route::post('create', [
                    'as' => 'folders.create',
                    'uses' => 'MediaFolderController@store',
                ]);
            });
        });
    });
});
