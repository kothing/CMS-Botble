<?php

namespace Botble\Base\Listeners;

use Botble\Base\Events\AdminNotificationEvent;
use Botble\Base\Models\AdminNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AdminNotificationListener
{
    public function handle(AdminNotificationEvent $event): void
    {
        $item = $event->item;

        if (! Cache::has('pruned_admin_notifications_table')) {
            (new AdminNotification())->pruneAll();

            Cache::put('pruned_admin_notifications_table', 1, Carbon::now()->addDay());
        }

        AdminNotification::query()->create([
            'title' => $item->getTitle(),
            'action_label' => $item->getLabel(),
            'action_url' => $item->getRoute(),
            'description' => $item->getDescription(),
            'permission' => $item->getPermission(),
        ]);
    }
}
