<?php

namespace Botble\Widget\Facades;

use Botble\Widget\WidgetGroup;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Botble\Widget\Factories\WidgetFactory registerWidget(string $widget)
 * @method static array getWidgets()
 * @method static \Illuminate\Support\HtmlString|string|null run()
 *
 * @see \Botble\Widget\Factories\WidgetFactory
 */
class Widget extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'botble.widget';
    }

    public static function group(string $name): WidgetGroup
    {
        return app('botble.widget-group-collection')->group($name);
    }
}
