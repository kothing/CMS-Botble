<?php

namespace Botble\Base\Events;

use Illuminate\Foundation\Events\Dispatchable;

class LicenseRevoking
{
    use Dispatchable;

    public function __construct(
        public string $licenseKey,
        public string $licenseName,
    ) {
    }
}
