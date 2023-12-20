<?php

namespace Botble\Base\Events;

use Illuminate\Foundation\Events\Dispatchable;

class SystemUpdateDBMigrated
{
    use Dispatchable;

    public function __construct()
    {
    }
}
