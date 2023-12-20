<?php

namespace Botble\Language\Listeners;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Language\Facades\Language;
use Exception;

class UpdatedContentListener
{
    public function handle(UpdatedContentEvent $event): void
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
