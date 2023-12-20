<?php

namespace Botble\JsValidation\Support;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\NestedRules;
use Illuminate\Validation\ValidationRuleParser;

class ValidationRuleParserProxy
{
    use AccessProtectedTrait;

    protected ValidationRuleParser $parser;

    protected Closure $parserMethod;

    public function __construct(array $data = [])
    {
        $this->parser = new ValidationRuleParser($data);
        $this->parserMethod = $this->createProtectedCaller($this->parser);
    }

    public function parse(array|string|Rule|NestedRules $rules): array
    {
        return $this->parser->parse($rules);
    }

    /**
     * Explode the rules into an array of explicit rules.
     *
     * @param array $rules
     * @return mixed
     */
    public function explodeRules($rules)
    {
        return $this->callProtected($this->parserMethod, 'explodeRules', [$rules]);
    }

    /**
     * Delegate method calls to parser instance.
     *
     * @param string $method
     * @param mixed $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        $arrCaller = [$this->parser, $method];

        return call_user_func_array($arrCaller, $params);
    }
}
