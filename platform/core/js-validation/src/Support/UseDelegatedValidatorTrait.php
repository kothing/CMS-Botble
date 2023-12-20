<?php

namespace Botble\JsValidation\Support;

trait UseDelegatedValidatorTrait
{
    /**
     * Delegated validator.
     *
     * @var DelegatedValidator $validator
     */
    protected $validator;

    /**
     * Sets delegated Validator instance.
     *
     * @param DelegatedValidator $validator
     * @return void
     */
    public function setDelegatedValidator(DelegatedValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Gets current DelegatedValidator instance.
     *
     * @return DelegatedValidator
     */
    public function getDelegatedValidator()
    {
        return $this->validator;
    }
}
