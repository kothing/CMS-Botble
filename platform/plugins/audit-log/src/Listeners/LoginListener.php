<?php

namespace Botble\AuditLog\Listeners;

use Botble\ACL\Models\User;
use Botble\AuditLog\Models\AuditHistory;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LoginListener
{
    public function __construct(protected Request $request)
    {
    }

    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        AuditHistory::query()->create([
            'user_agent' => $this->request->userAgent(),
            'ip_address' => $this->request->ip(),
            'module' => 'to the system',
            'action' => 'logged in',
            'user_id' => $user->getKey(),
            'reference_user' => 0,
            'reference_id' => $user->getKey(),
            'reference_name' => $user->name,
            'type' => 'info',
        ]);
    }
}
