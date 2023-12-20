<?php

namespace Botble\Base\Supports\ValueObjects;

use Botble\Base\Supports\Core;
use Carbon\CarbonInterface;

class CoreProduct
{
    public function __construct(
        public string $updateId,
        public string $version,
        public CarbonInterface $releasedDate,
        public string|null $summary = null,
        public string|null $changelog = null,
        public bool $hasSQL = false
    ) {
    }

    public function hasUpdate(): bool
    {
        return version_compare($this->version, Core::make()->version(), '>');
    }
}
