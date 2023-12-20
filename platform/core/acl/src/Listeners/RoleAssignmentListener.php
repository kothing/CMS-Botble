<?php

namespace Botble\ACL\Listeners;

use Botble\ACL\Events\RoleAssignmentEvent;
use Illuminate\Support\Facades\Auth;

class RoleAssignmentListener
{
    public function handle(RoleAssignmentEvent $event): void
    {
        $permissions = $event->role->permissions;
        $permissions[ACL_ROLE_SUPER_USER] = $event->user->super_user;
        $permissions[ACL_ROLE_MANAGE_SUPERS] = $event->user->manage_supers;

        $event->user->permissions = $permissions;
        $event->user->save();

        cache()->forget(md5('cache-dashboard-menu-' . Auth::id()));
    }
}
