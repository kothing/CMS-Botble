<?php

namespace Botble\Language\Providers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Language\Listeners\ActivatedPluginListener;
use Botble\Language\Listeners\AddHrefLangListener;
use Botble\Language\Listeners\CreatedContentListener;
use Botble\Language\Listeners\DeletedContentListener;
use Botble\Language\Listeners\ThemeRemoveListener;
use Botble\Language\Listeners\UpdatedContentListener;
use Botble\PluginManagement\Events\ActivatedPluginEvent;
use Botble\Theme\Events\RenderingSingleEvent;
use Botble\Theme\Events\ThemeRemoveEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UpdatedContentEvent::class => [
            UpdatedContentListener::class,
        ],
        CreatedContentEvent::class => [
            CreatedContentListener::class,
        ],
        DeletedContentEvent::class => [
            DeletedContentListener::class,
        ],
        ThemeRemoveEvent::class => [
            ThemeRemoveListener::class,
        ],
        ActivatedPluginEvent::class => [
            ActivatedPluginListener::class,
        ],
        RenderingSingleEvent::class => [
            AddHrefLangListener::class,
        ],
    ];
}
