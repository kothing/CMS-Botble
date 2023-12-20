<?php

use Botble\Base\Facades\BaseHelper;
use Botble\LanguageAdvanced\Http\Controllers\LanguageAdvancedController;
use Illuminate\Support\Facades\Route;

Route::group([
    'controller' => LanguageAdvancedController::class,
    'prefix' => BaseHelper::getAdminPrefix() . '/language-advanced',
    'middleware' => ['web', 'core', 'auth'],
], function () {
    Route::post('save/{id}', [
        'as' => 'language-advanced.save',
        'uses' => 'save',
        'permission' => false,
    ])->wherePrimaryKey();
});
