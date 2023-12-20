<?php

namespace Botble\Base\Events;

use Illuminate\Foundation\Events\Dispatchable;

class SystemUpdateCachesCleared
{
    use Dispatchable;

    public function __construct()
    {
    }
}
