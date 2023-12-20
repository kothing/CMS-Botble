<?php

use Botble\Installer\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'install',
    'as' => 'installers.',
    'controller' => InstallController::class,
    'middleware' => ['web', 'core'],
], function () {
    Route::group(['middleware' => 'install'], function () {
        Route::get('/', 'getWelcome')->name('welcome');

        Route::get('requirements', 'getRequirements')->name('requirements');

        Route::get('environment', 'getEnvironment')->name('environment');

        Route::post('environment/save', 'postSaveEnvironment')->name('environment.save');
    });

    Route::group(['middleware' => 'installing'], function () {
        Route::get('account', 'getCreateAccount')->name('create_account');

        Route::post('account/save', 'postSaveAccount')->name('account.save');

        Route::get('final', 'getFinish')->name('final');
    });
});
