<?php

namespace Botble\Language\Listeners;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Language\Facades\Language;
use Exception;

class CreatedContentListener
{
    public function handle(CreatedContentEvent $event): void
    {
        try {
            if ($event->request->input('language')) {
                Language::saveLanguage($event->screen, $event->request, $event->data);
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
