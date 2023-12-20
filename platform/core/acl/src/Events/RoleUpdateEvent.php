<?php

namespace Botble\ACL\Events;

use Botble\ACL\Models\Role;
use Botble\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

class RoleUpdateEvent extends Event
{
    use SerializesModels;

    public function __construct(public Role $role)
    {
    }
}
