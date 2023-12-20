<?php

namespace Botble\JsValidation\Support;

use Closure;
use Illuminate\Validation\Validator as BaseValidator;

class DelegatedValidator
{
    use AccessProtectedTrait;

    protected Closure $validatorMethod;

    public function __construct(protected BaseValidator $validator, protected ValidationRuleParserProxy $ruleParser)
    {
        $this->validatorMethod = $this->createProtectedCaller($validator);
    }

    public function getValidator(): BaseValidator
    {
        return $this->validator;
    }

    public function getData(): array
    {
        return $this->validator->getData();
    }

    public function setData(array $data): void
    {
        $this->validator->setData($data);
    }

    public function getRules(): array
    {
        return $this->validator->getRules();
    }

    /**
     * Determine if a given rule implies the attribute is required.
     *
     * @param string $rule
     * @return bool
     */
    public function isImplicit($rule)
    {
        return $this->callValidator('isImplicit', [$rule]);
    }

    /**
     * Call validator method.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    private function callValidator($method, $args = [])
    {
        return $this->callProtected($this->validatorMethod, $method, $args);
    }

    /**
     * Replace all error message place-holders with actual values.
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    public function makeReplacements($message, $attribute, $rule, $parameters)
    {
        if (is_object($rule)) {
            $rule = get_class($rule);
        }

        return $this->callValidator('makeReplacements', [$message, $attribute, $rule, $parameters]);
    }

    /**
     * Determine if the given attribute has a rule in the given set.
     *
     * @param string $attribute
     * @param string|array $rules
     * @return bool
     */
    public function hasRule($attribute, $rules)
    {
        return $this->callValidator('hasRule', [$attribute, $rules]);
    }

    /**
     * Get the validation message for an attribute and rule.
     *
     * @param string $attribute
     * @param string $rule
     * @return string
     */
    public function getMessage($attribute, $rule)
    {
        if (is_object($rule)) {
            $rule = get_class($rule);
        }

        return $this->callValidator('getMessage', [$attribute, $rule]);
    }

    /**
     * Extract the rule name and parameters from a rule.
     *
     * @param array|string $rules
     * @return array
     */
    public function parseRule($rules)
    {
        return $this->ruleParser->parse($rules);
    }

    /**
     * Explode the rules into an array of rules.
     *
     * @param string|array $rules
     * @return array
     */
    public function explodeRules($rules)
    {
        return $this->ruleParser->explodeRules($rules);
    }

    /**
     * Add conditions to a given field based on a Closure.
     *
     * @param string $attribute
     * @param string|array $rules
     * @param callable $callback
     * @return void
     */
    public function sometimes($attribute, $rules, callable $callback)
    {
        $this->validator->sometimes($attribute, $rules, $callback);
    }

    /**
     * Delegate method calls to validator instance.
     *
     * @param $method
     * @param $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        $arrCaller = [$this->validator, $method];

        return call_user_func_array($arrCaller, $params);
    }
}
