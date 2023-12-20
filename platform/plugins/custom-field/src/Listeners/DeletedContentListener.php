<?php

namespace Botble\CustomField\Listeners;

use Botble\Base\Events\DeletedContentEvent;
use Botble\CustomField\Facades\CustomField;
use Exception;

class DeletedContentListener
{
    public function handle(DeletedContentEvent $event): void
    {
        try {
            CustomField::deleteCustomFields($event->data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
