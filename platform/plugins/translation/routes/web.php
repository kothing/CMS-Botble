<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Translation\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'translations'], function () {
            Route::group(['prefix' => 'locales', 'permission' => 'translations.locales', ], function () {
                Route::get('', [
                    'as' => 'translations.locales',
                    'uses' => 'TranslationController@getLocales',
                ]);

                Route::post('', [
                    'as' => 'translations.locales.post',
                    'uses' => 'TranslationController@postLocales',
                    'middleware' => 'preventDemo',
                ]);

                Route::delete('{locale}', [
                    'as' => 'translations.locales.delete',
                    'uses' => 'TranslationController@deleteLocale',
                    'middleware' => 'preventDemo',
                ]);

                Route::get('download/{locale}', [
                    'as' => 'translations.locales.download',
                    'uses' => 'TranslationController@downloadLocale',
                    'middleware' => 'preventDemo',
                ]);

                Route::get('ajax/available-remote-locales', [
                    'as' => 'translations.locales.available-remote-locales',
                    'uses' => 'TranslationController@ajaxGetAvailableRemoteLocales',
                ]);

                Route::post('ajax/download-remote-locale/{locale}', [
                    'as' => 'translations.locales.download-remote-locale',
                    'uses' => 'TranslationController@ajaxDownloadRemoteLocale',
                    'middleware' => 'preventDemo',
                ]);
            });

            Route::group(['prefix' => 'admin', 'permission' => 'translations.index', ], function () {
                Route::get('/', [
                    'as' => 'translations.index',
                    'uses' => 'TranslationController@getIndex',
                ]);

                Route::post('edit', [
                    'as' => 'translations.group.edit',
                    'uses' => 'TranslationController@update',
                ]);

                Route::post('publish', [
                    'as' => 'translations.group.publish',
                    'uses' => 'TranslationController@postPublish',
                    'middleware' => 'preventDemo',
                ]);

                Route::post('import', [
                    'as' => 'translations.import',
                    'uses' => 'TranslationController@postImport',

                ]);
            });

            Route::group(['prefix' => 'theme', 'permission' => 'translations.theme-translations'], function () {
                Route::match(['GET', 'POST'], '', [
                    'as' => 'translations.theme-translations',
                    'uses' => 'TranslationController@getThemeTranslations',
                ]);

                Route::post('post', [
                    'as' => 'translations.theme-translations.post',
                    'uses' => 'TranslationController@postThemeTranslations',
                    'middleware' => 'preventDemo',
                ]);
            });
        });
    });
});
