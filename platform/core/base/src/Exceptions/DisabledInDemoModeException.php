<?php

namespace Botble\Base\Exceptions;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Throwable;

class DisabledInDemoModeException extends BadRequestException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(trans('core/base::system.disabled_in_demo_mode') . $message, $code, $previous);
    }
}
