<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Botble\Member\Http\Controllers',
    'prefix' => BaseHelper::getAdminPrefix(),
    'middleware' => ['web', 'core', 'auth'],
], function () {
    Route::group(['prefix' => 'members', 'as' => 'member.'], function () {
        Route::resource('', 'MemberController')->parameters(['' => 'member']);

        Route::delete('items/destroy', [
            'as' => 'deletes',
            'uses' => 'MemberController@deletes',
            'permission' => 'member.destroy',
        ]);
    });
});

if (defined('THEME_MODULE_SCREEN_NAME')) {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::group([
            'namespace' => 'Botble\Member\Http\Controllers',
            'middleware' => ['web', 'core'],
            'as' => 'public.member.',
        ], function () {
            Route::group(['middleware' => ['member.guest']], function () {
                Route::get('login', 'LoginController@showLoginForm')->name('login');
                Route::post('login', 'LoginController@login')->name('login.post');

                Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
                Route::post('register', 'RegisterController@register')->name('register.post');

                Route::get(
                    'password/request',
                    'ForgotPasswordController@showLinkRequestForm'
                )->name('password.request');
                Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
                Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');
                Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
            });

            Route::group([
                'middleware' => [
                    setting(
                        'verify_account_email',
                        config('plugins.member.general.verify_email')
                    ) ? 'member.guest' : 'member',
                ],
            ], function () {
                Route::get(
                    'register/confirm/resend',
                    'RegisterController@resendConfirmation'
                )->name('resend_confirmation');
                Route::get('register/confirm/{user}', 'RegisterController@confirm')->name('confirm');
            });
        });

        Route::group([
            'namespace' => 'Botble\Member\Http\Controllers',
            'middleware' => ['web', 'core', 'member'],
            'as' => 'public.member.',
        ], function () {
            Route::group([
                'prefix' => 'account',
            ], function () {
                Route::post('logout', 'LoginController@logout')->name('logout');

                Route::get('dashboard', [
                    'as' => 'dashboard',
                    'uses' => 'PublicController@getDashboard',
                ]);

                Route::get('settings', [
                    'as' => 'settings',
                    'uses' => 'PublicController@getSettings',
                ]);

                Route::post('settings', [
                    'as' => 'post.settings',
                    'uses' => 'PublicController@postSettings',
                ]);

                Route::get('security', [
                    'as' => 'security',
                    'uses' => 'PublicController@getSecurity',
                ]);

                Route::put('security', [
                    'as' => 'post.security',
                    'uses' => 'PublicController@postSecurity',
                ]);

                Route::post('avatar', [
                    'as' => 'avatar',
                    'uses' => 'PublicController@postAvatar',
                ]);
            });

            Route::group(['prefix' => 'ajax/members'], function () {
                Route::get('activity-logs', [
                    'as' => 'activity-logs',
                    'uses' => 'PublicController@getActivityLogs',
                ]);

                Route::post('upload', [
                    'as' => 'upload',
                    'uses' => 'PublicController@postUpload',
                ]);

                Route::post('upload-from-editor', [
                    'as' => 'upload-from-editor',
                    'uses' => 'PublicController@postUploadFromEditor',
                ]);
            });

            if (is_plugin_active('blog')) {
                Route::group([
                    'prefix' => 'account/posts',
                    'as' => 'posts.',
                ], function () {
                    Route::resource('', 'PostController')->parameters(['' => 'post']);
                });

                Route::group(['prefix' => 'ajax/members'], function () {
                    Route::get('tags/all', [
                        'as' => 'tags.all',
                        'uses' => 'PostController@getAllTags',
                    ]);
                });
            }
        });
    });
}
