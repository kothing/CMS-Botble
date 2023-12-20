<?php

namespace Botble\Widget\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Botble\Widget\WidgetGroup group(string $sidebarId)
 * @method static \Botble\Widget\WidgetGroupCollection setGroup(array $args)
 * @method static \Botble\Widget\WidgetGroupCollection removeGroup(string $groupId)
 * @method static array getGroups()
 * @method static string render(string $sidebarId)
 * @method static void load(bool $force = false)
 * @method static \Illuminate\Support\Collection getData()
 *
 * @see \Botble\Widget\WidgetGroupCollection
 */
class WidgetGroup extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'botble.widget-group-collection';
    }
}
