<?php

namespace Botble\Base\Exceptions;

use RuntimeException;

class MissingCURLExtensionException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('cURL extension is not installed.');
    }
}
