<?php

namespace Botble\JsValidation\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Botble\JsValidation\Javascript\JavascriptValidator make(array $rules, array $messages = [], array $customAttributes = [], string|null $selector = null)
 * @method static \Botble\JsValidation\Javascript\JavascriptValidator formRequest($formRequest, $selector = null)
 * @method static \Botble\JsValidation\Javascript\JavascriptValidator validator(\Illuminate\Validation\Validator $validator, string|null $selector = null)
 *
 * @see \Botble\JsValidation\JsValidatorFactory
 */
class JsValidator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'js-validator';
    }
}
