<?php

namespace Botble\Media\Chunks\Exceptions;

use Exception;

class UploadMissingFileException extends Exception
{
    public function __construct(string $message = 'The request is missing a file', int $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
