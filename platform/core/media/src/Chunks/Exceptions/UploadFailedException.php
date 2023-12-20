<?php

namespace Botble\Media\Chunks\Exceptions;

use Exception;
use Throwable;

class UploadFailedException extends Exception
{
    public function __construct(string $message, int $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
