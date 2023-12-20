<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Backup\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'system/backups'], function () {
            Route::get('', [
                'as' => 'backups.index',
                'uses' => 'BackupController@getIndex',
            ]);

            Route::post('create', [
                'as' => 'backups.create',
                'uses' => 'BackupController@store',
                'middleware' => 'preventDemo',
            ]);

            Route::delete('delete/{folder}', [
                'as' => 'backups.destroy',
                'uses' => 'BackupController@destroy',
                'middleware' => 'preventDemo',
            ]);

            Route::get('restore/{folder}', [
                'as' => 'backups.restore',
                'uses' => 'BackupController@getRestore',
            ]);

            Route::get('download-database/{folder}', [
                'as' => 'backups.download.database',
                'uses' => 'BackupController@getDownloadDatabase',
                'middleware' => 'preventDemo',
                'permission' => 'backups.index',
            ]);

            Route::get('download-uploads-folder/{folder}', [
                'as' => 'backups.download.uploads.folder',
                'uses' => 'BackupController@getDownloadUploadFolder',
                'middleware' => 'preventDemo',
                'permission' => 'backups.index',
            ]);
        });
    });
});
