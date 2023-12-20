<?php

namespace Botble\LanguageAdvanced\Providers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\LanguageAdvanced\Listeners\AddDefaultTranslations;
use Botble\LanguageAdvanced\Listeners\ClearCacheAfterUpdateData;
use Botble\LanguageAdvanced\Listeners\PriorityLanguageAdvancedPluginListener;
use Botble\PluginManagement\Events\ActivatedPluginEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CreatedContentEvent::class => [
            AddDefaultTranslations::class,
        ],
        UpdatedContentEvent::class => [
            ClearCacheAfterUpdateData::class,
        ],
        ActivatedPluginEvent::class => [
            PriorityLanguageAdvancedPluginListener::class,
        ],
    ];
}
