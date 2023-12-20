<?php

namespace Botble\Menu\Facades;

use Botble\Menu\Menu as BaseMenu;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool hasMenu(string $slug)
 * @method static array recursiveSaveMenu(array $menuNodes, string|int $menuId, string|int $parentId)
 * @method static \Botble\Menu\Models\MenuNode getReferenceMenuNode(array $item, \Botble\Menu\Models\MenuNode $menuNode)
 * @method static \Botble\Menu\Menu addMenuLocation(string $location, string $description)
 * @method static array getMenuLocations()
 * @method static \Botble\Menu\Menu removeMenuLocation(string $location)
 * @method static string renderMenuLocation(string $location, array $attributes = [])
 * @method static bool isLocationHasMenu(string $location)
 * @method static void load(bool $force = false)
 * @method static string|null generateMenu(array $args = [])
 * @method static void registerMenuOptions(string $model, string $name)
 * @method static string|null generateSelect(array $args = [])
 * @method static \Botble\Menu\Menu addMenuOptionModel(string $model)
 * @method static array getMenuOptionModels()
 * @method static \Botble\Menu\Menu setMenuOptionModels(array $models)
 * @method static \Botble\Menu\Menu clearCacheMenuItems()
 *
 * @see \Botble\Menu\Menu
 */
class Menu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaseMenu::class;
    }
}
