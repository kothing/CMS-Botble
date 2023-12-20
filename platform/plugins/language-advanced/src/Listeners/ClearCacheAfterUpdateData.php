<?php

namespace Botble\LanguageAdvanced\Listeners;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Support\Services\Cache\Cache;

class ClearCacheAfterUpdateData
{
    public function handle(UpdatedContentEvent $event): void
    {
        if (! setting('enable_cache', false)) {
            return;
        }

        $cache = new Cache(app('cache'), get_class($event->data));
        $cache->flush();
    }
}
