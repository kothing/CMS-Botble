<?php

namespace Botble\Menu\Providers;

use Botble\Base\Events\DeletedContentEvent;
use Botble\Menu\Listeners\DeleteMenuNodeListener;
use Botble\Menu\Listeners\UpdateMenuNodeUrlListener;
use Botble\Slug\Events\UpdatedSlugEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UpdatedSlugEvent::class => [
            UpdateMenuNodeUrlListener::class,
        ],
        DeletedContentEvent::class => [
            DeleteMenuNodeListener::class,
        ],
    ];
}
