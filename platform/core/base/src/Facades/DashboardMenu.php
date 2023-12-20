<?php

namespace Botble\Base\Facades;

use Botble\Base\Supports\DashboardMenu as DashboardMenuSupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Botble\Base\Supports\DashboardMenu make()
 * @method static \Botble\Base\Supports\DashboardMenu registerItem(array $options)
 * @method static \Botble\Base\Supports\DashboardMenu removeItem(array|string $id, $parentId = null)
 * @method static bool hasItem(string $id, string|null $parentId = null)
 * @method static \Illuminate\Support\Collection getAll()
 *
 * @see \Botble\Base\Supports\DashboardMenu
 */
class DashboardMenu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DashboardMenuSupport::class;
    }
}
