<?php

namespace Botble\JsValidation;

use Botble\JsValidation\Javascript\JavascriptValidator;
use Botble\JsValidation\Javascript\MessageParser;
use Botble\JsValidation\Javascript\RuleParser;
use Botble\JsValidation\Javascript\ValidatorHandler;
use Botble\JsValidation\Support\DelegatedValidator;
use Botble\JsValidation\Support\ValidationRuleParserProxy;
use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

class JsValidatorFactory
{
    public const ASTERISK = '__asterisk__';

    protected array $options = [];

    public function __construct(protected Container $app, array $options = [])
    {
        $this->setOptions($options);
    }

    protected function setOptions(array $options): void
    {
        $options['disable_remote_validation'] = empty($options['disable_remote_validation']) ? false : $options['disable_remote_validation'];
        $options['view'] = empty($options['view']) ? 'core/js-validation:bootstrap' : $options['view'];
        $options['form_selector'] = empty($options['form_selector']) ? 'form' : $options['form_selector'];

        $this->options = $options;
    }

    /**
     * Creates JsValidator instance based on rules and message arrays.
     */
    public function make(array $rules, array $messages = [], array $customAttributes = [], string|null $selector = null): JavascriptValidator
    {
        $validator = $this->getValidatorInstance($rules, $messages, $customAttributes);

        return $this->validator($validator, $selector);
    }

    /**
     * Get the validator instance for the request.
     */
    protected function getValidatorInstance(array $rules, array $messages = [], array $customAttributes = [])
    {
        $factory = $this->app->make(ValidationFactory::class);

        $data = $this->getValidationData($rules, $customAttributes);
        $validator = $factory->make($data, $rules, $messages, $customAttributes);
        $validator->addCustomAttributes($customAttributes);

        return $validator;
    }

    /**
     * Gets fake data when validator has wildcard rules.
     */
    protected function getValidationData(array $rules, array $customAttributes = [])
    {
        $attributes = array_filter(array_keys($rules), function ($attribute) {
            return $attribute !== '' && mb_strpos($attribute, '*') !== false;
        });

        $attributes = array_merge(array_keys($customAttributes), $attributes);

        return array_reduce($attributes, function ($data, $attribute) {
            // Prevent wildcard rule being removed as an implicit attribute (not present in the data).
            $attribute = str_replace('*', self::ASTERISK, $attribute);

            Arr::set($data, $attribute, true);

            return $data;
        }, []);
    }

    /**
     * Creates JsValidator instance based on FormRequest.
     */
    public function formRequest($formRequest, $selector = null): JavascriptValidator
    {
        if (! is_object($formRequest)) {
            $formRequest = $this->createFormRequest($formRequest);
        }

        $rules = method_exists($formRequest, 'rules') ? $formRequest->rules() : [];

        $rules = apply_filters('core_request_rules', $rules, $formRequest);

        $messages = apply_filters('core_request_messages', $formRequest->messages(), $formRequest);

        $attributes = apply_filters('core_request_attributes', $formRequest->attributes(), $formRequest);

        $validator = $this->getValidatorInstance($rules, $messages, $attributes);

        return $this->validator($validator, $selector);
    }

    protected function parseFormRequestName(string|array $class): array
    {
        $params = [];
        if (is_array($class)) {
            $params = empty($class[1]) ? $params : $class[1];
            $class = $class[0];
        }

        return [$class, $params];
    }

    /**
     * Creates and initializes a Form Request instance.
     */
    protected function createFormRequest(string $class): FormRequest
    {
        /*
         * @var $formRequest \Illuminate\Foundation\Http\FormRequest
         * @var $request Request
         */
        [$class, $params] = $this->parseFormRequestName($class);

        $request = $this->app->__get('request');
        // @phpstan-ignore-next-line
        $formRequest = $this->app->build($class, $params);

        if ($session = $request->getSession()) {
            $formRequest->setLaravelSession($session);
        }
        $formRequest->setUserResolver($request->getUserResolver());
        $formRequest->setRouteResolver($request->getRouteResolver());
        $formRequest->setContainer($this->app);
        $formRequest->query = $request->query;

        return $formRequest;
    }

    /**
     * Creates JsValidator instance based on Validator.
     */
    public function validator(Validator $validator, string|null $selector = null): JavascriptValidator
    {
        return $this->jsValidator($validator, $selector);
    }

    /**
     * Creates JsValidator instance based on Validator.
     */
    protected function jsValidator(Validator $validator, string|null $selector = null): JavascriptValidator
    {
        $remote = ! $this->options['disable_remote_validation'];
        $view = $this->options['view'];
        $selector = is_null($selector) ? $this->options['form_selector'] : $selector;
        $ignore = Arr::get($this->options, 'ignore');

        $delegated = new DelegatedValidator($validator, new ValidationRuleParserProxy($validator->getData()));
        $rules = new RuleParser($delegated, $this->getSessionToken());
        $messages = new MessageParser($delegated, $this->options['escape'] ?? false);

        $jsValidator = new ValidatorHandler($rules, $messages);

        return new JavascriptValidator($jsValidator, compact('view', 'selector', 'remote', 'ignore'));
    }

    /**
     * Get and encrypt token from session store.
     */
    protected function getSessionToken(): string|null
    {
        $token = null;
        if ($session = $this->app->__get('session')) {
            $token = $session->token();
        }

        if ($encrypter = $this->app->__get('encrypter')) {
            $token = $encrypter->encrypt($token);
        }

        return $token;
    }
}
