<?php

use Botble\LanguageAdvanced\Http\Controllers\LanguageAdvancedController;
use Illuminate\Support\Facades\Route;

Route::group([
    'controller' => LanguageAdvancedController::class,
    'middleware' => ['web', 'core', 'member'],
    'prefix' => 'account',
    'as' => 'public.member.language-advanced.',
], function () {
    Route::post('language-advanced/save/{id}', ['as' => 'save', 'uses' => 'save'])->wherePrimaryKey();
});
