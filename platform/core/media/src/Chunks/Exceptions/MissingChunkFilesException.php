<?php

namespace Botble\Media\Chunks\Exceptions;

use Exception;
use Throwable;

class MissingChunkFilesException extends Exception
{
    public function __construct(
        string $message = 'Logic did not find any chunk file - check the folder configuration',
        int $code = 500,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
