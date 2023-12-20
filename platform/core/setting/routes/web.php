<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Setting\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'settings'], function () {
            Route::get('general', [
                'as' => 'settings.options',
                'uses' => 'SettingController@getOptions',
                'permission' => 'settings.options',
            ]);

            Route::post('general/edit', [
                'as' => 'settings.edit',
                'uses' => 'SettingController@postEdit',
                'permission' => 'settings.options',
            ]);

            Route::get('media', [
                'as' => 'settings.media',
                'uses' => 'SettingController@getMediaSetting',
            ]);

            Route::post('media', [
                'as' => 'settings.media.post',
                'uses' => 'SettingController@postEditMediaSetting',
                'permission' => 'settings.media',
                'middleware' => 'preventDemo',
            ]);

            Route::post('media/generate-thumbnails', [
                'as' => 'settings.media.generate-thumbnails',
                'uses' => 'SettingController@generateThumbnails',
                'permission' => 'settings.media',
                'middleware' => 'preventDemo',
            ]);

            Route::get('license/verify', [
                'as' => 'settings.license.verify',
                'uses' => 'SettingController@getVerifyLicense',
                'permission' => false,
            ]);

            Route::post('license/activate', [
                'as' => 'settings.license.activate',
                'uses' => 'SettingController@activateLicense',
                'middleware' => 'preventDemo',
                'permission' => 'core.manage.license',
            ]);

            Route::post('license/deactivate', [
                'as' => 'settings.license.deactivate',
                'uses' => 'SettingController@deactivateLicense',
                'middleware' => 'preventDemo',
                'permission' => 'core.manage.license',
            ]);

            Route::post('license/reset', [
                'as' => 'settings.license.reset',
                'uses' => 'SettingController@resetLicense',
                'middleware' => 'preventDemo',
                'permission' => 'core.manage.license',
            ]);

            Route::group(['prefix' => 'email', 'permission' => 'settings.email'], function () {
                Route::get('', [
                    'as' => 'settings.email',
                    'uses' => 'SettingController@getEmailConfig',
                ]);

                Route::match(['POST', 'GET'], 'templates/preview/{type}/{module}/{template}', [
                    'as' => 'setting.email.preview',
                    'uses' => 'SettingController@previewEmailTemplate',
                ]);

                Route::get('templates/preview/{type}/{module}/{template}/iframe', [
                    'as' => 'setting.email.preview.iframe',
                    'uses' => 'SettingController@previewEmailTemplateIframe',
                ]);

                Route::post('edit', [
                    'as' => 'settings.email.edit',
                    'uses' => 'SettingController@postEditEmailConfig',
                ]);

                Route::get('templates/edit/{type}/{module}/{template}', [
                    'as' => 'setting.email.template.edit',
                    'uses' => 'SettingController@getEditEmailTemplate',
                ]);

                Route::post('template/edit', [
                    'as' => 'setting.email.template.store',
                    'uses' => 'SettingController@postStoreEmailTemplate',
                    'middleware' => 'preventDemo',
                ]);

                Route::post('template/reset-to-default', [
                    'as' => 'setting.email.template.reset-to-default',
                    'uses' => 'SettingController@postResetToDefault',
                    'middleware' => 'preventDemo',
                ]);

                Route::post('status', [
                    'as' => 'setting.email.status.change',
                    'uses' => 'SettingController@postChangeEmailStatus',
                ]);

                Route::post('test/send', [
                    'as' => 'setting.email.send.test',
                    'uses' => 'SettingController@postSendTestEmail',
                ]);
            });

            Route::get('cronjob', [
                'as' => 'settings.cronjob',
                'uses' => 'SettingController@cronjob',
                'permission' => 'settings.cronjob',
            ]);
        });
    });
});
