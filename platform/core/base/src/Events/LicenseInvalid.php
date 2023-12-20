<?php

namespace Botble\Base\Events;

use Illuminate\Foundation\Events\Dispatchable;

class LicenseInvalid
{
    use Dispatchable;

    public function __construct(
        public string $licenseKey,
        public string $licenseName,
    ) {
    }
}
