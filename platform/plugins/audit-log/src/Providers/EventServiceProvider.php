<?php

namespace Botble\AuditLog\Providers;

use Botble\AuditLog\Events\AuditHandlerEvent;
use Botble\AuditLog\Listeners\AuditHandlerListener;
use Botble\AuditLog\Listeners\CreatedContentListener;
use Botble\AuditLog\Listeners\DeletedContentListener;
use Botble\AuditLog\Listeners\LoginListener;
use Botble\AuditLog\Listeners\UpdatedContentListener;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AuditHandlerEvent::class => [
            AuditHandlerListener::class,
        ],
        Login::class => [
            LoginListener::class,
        ],
        UpdatedContentEvent::class => [
            UpdatedContentListener::class,
        ],
        CreatedContentEvent::class => [
            CreatedContentListener::class,
        ],
        DeletedContentEvent::class => [
            DeletedContentListener::class,
        ],
    ];
}
