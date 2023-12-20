<?php

namespace Botble\Support\Http\Requests;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function createDefaultValidator(ValidationFactory $factory)
    {
        $rules = method_exists($this, 'rules') ? $this->container->call([$this, 'rules']) : [];

        $rules = apply_filters('core_request_rules', $rules, $this);

        $messages = apply_filters('core_request_messages', $this->messages(), $this);

        $attributes = apply_filters('core_request_attributes', $this->attributes(), $this);

        $validationData = apply_filters('core_request_validation_data', $this->validationData(), $this);

        // @phpstan-ignore-next-line
        $validator = $factory->make(
            $validationData,
            $rules,
            $messages,
            $attributes
        )->stopOnFirstFailure($this->stopOnFirstFailure);

        if ($this->isPrecognitive()) {
            $validator->setRules(
                $this->filterPrecognitiveRules($validator->getRulesWithoutPlaceholders())
            );
        }

        return $validator;
    }
}
