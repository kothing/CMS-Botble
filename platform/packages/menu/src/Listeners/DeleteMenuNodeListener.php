<?php

namespace Botble\Menu\Listeners;

use Botble\Base\Events\DeletedContentEvent;
use Botble\Menu\Facades\Menu;
use Botble\Menu\Models\MenuNode;

class DeleteMenuNodeListener
{
    public function handle(DeletedContentEvent $event): void
    {
        if (! in_array(get_class($event->data), Menu::getMenuOptionModels())) {
            return;
        }

        MenuNode::query()
            ->where([
                'reference_id' => $event->data->getKey(),
                'reference_type' => get_class($event->data),
            ])
            ->delete();
    }
}
