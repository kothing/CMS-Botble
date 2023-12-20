<?php

namespace Botble\AuditLog\Events;

use Botble\Base\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class AuditHandlerEvent extends Event
{
    use SerializesModels;

    public string|int $referenceUser;

    public function __construct(
        public string $module,
        public string $action,
        public int|string $referenceId,
        public string|null $referenceName,
        public string $type,
        int|string $referenceUser = 0
    ) {
        if ($referenceUser === 0 && Auth::check()) {
            $referenceUser = Auth::id();
        }

        $this->referenceUser = $referenceUser;
    }
}
