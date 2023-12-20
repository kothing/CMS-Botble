<?php

namespace Botble\JsValidation\Exceptions;

use Exception;

class PropertyNotFoundException extends Exception
{
    public function __construct(string $property = '', $caller = '', Exception $previous = null)
    {
        $message = '"' . $property . '" not found in "' . $caller . '" object';

        parent::__construct($message, 0, $previous);
    }
}
