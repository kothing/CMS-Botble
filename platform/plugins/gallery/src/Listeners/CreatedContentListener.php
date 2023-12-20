<?php

namespace Botble\Gallery\Listeners;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Gallery\Facades\Gallery;
use Exception;

class CreatedContentListener
{
    public function handle(CreatedContentEvent $event): void
    {
        try {
            Gallery::saveGallery($event->request, $event->data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
