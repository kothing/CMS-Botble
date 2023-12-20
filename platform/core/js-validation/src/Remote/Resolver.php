<?php

namespace Botble\JsValidation\Remote;

use Botble\JsValidation\Support\AccessProtectedTrait;
use Closure;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator as BaseValidator;

class Resolver
{
    use AccessProtectedTrait;

    protected ?Closure $resolver = null;

    public function __construct(protected ValidationFactory $factory, protected bool $escape = false)
    {
        $this->resolver = $this->getProtected($factory, 'resolver');
    }

    /**
     * Closure used to resolve Validator instance.
     *
     * @param $field
     * @return Closure
     */
    public function resolver($field)
    {
        return function ($translator, $data, $rules, $messages, $customAttributes) use ($field) {
            return $this->resolve($translator, $data, $rules, $messages, $customAttributes, $field);
        };
    }

    /**
     * Resolves Validator instance.
     *
     * @param $translator
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @param string $field
     * @return BaseValidator
     */
    protected function resolve($translator, $data, $rules, $messages, $customAttributes, $field)
    {
        $validateAll = Arr::get($data, $field . '_validate_all', false);
        $validationRule = 'bail|' . Validator::EXTENSION_NAME . ':' . $validateAll;
        $rules = [$field => $validationRule] + $rules;

        return $this->createValidator($translator, $data, $rules, $messages, $customAttributes);
    }

    /**
     * Create new validator instance.
     *
     * @param $translator
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return BaseValidator
     */
    protected function createValidator($translator, $data, $rules, $messages, $customAttributes)
    {
        if (empty($this->resolver)) {
            return new BaseValidator($translator, $data, $rules, $messages, $customAttributes);
        }

        return call_user_func($this->resolver, $translator, $data, $rules, $messages, $customAttributes);
    }

    /**
     * Closure used to trigger JsValidations.
     *
     * @return Closure
     */
    public function validatorClosure()
    {
        return function ($attribute, $value, $parameters, BaseValidator $validator) {
            $remoteValidator = new Validator($validator, $this->escape);
            $remoteValidator->validate($value, $parameters);

            return $attribute;
        };
    }
}
