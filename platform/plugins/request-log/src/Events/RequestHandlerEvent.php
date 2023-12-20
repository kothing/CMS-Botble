<?php

namespace Botble\RequestLog\Events;

use Botble\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

class RequestHandlerEvent extends Event
{
    use SerializesModels;

    public function __construct(public int $code)
    {
    }
}
