<?php

namespace Botble\Gallery\Listeners;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Gallery\Facades\Gallery;
use Exception;

class UpdatedContentListener
{
    public function handle(UpdatedContentEvent $event): void
    {
        try {
            Gallery::saveGallery($event->request, $event->data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
