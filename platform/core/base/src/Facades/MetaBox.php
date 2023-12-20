<?php

namespace Botble\Base\Facades;

use Botble\Base\Supports\MetaBox as MetaBoxSupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void addMetaBox(string $id, string $title, \Closure|callable|array|string $callback, string|null $reference = null, string $context = 'advanced', string $priority = 'default', array|null $callbackArgs = null)
 * @method static void doMetaBoxes(string $context, \Illuminate\Database\Eloquent\Model|string|null $object = null)
 * @method static void removeMetaBox(string $id, string|null $reference, string $context)
 * @method static void saveMetaBoxData(\Illuminate\Database\Eloquent\Model $object, string $key, $value, $options = null)
 * @method static array|string|null getMetaData(\Illuminate\Database\Eloquent\Model $object, string $key, bool $single = false, array $select = ['meta_value'])
 * @method static \Illuminate\Database\Eloquent\Model|null getMeta(\Illuminate\Database\Eloquent\Model $object, string $key, array $select = ['meta_value'])
 * @method static bool deleteMetaData(\Illuminate\Database\Eloquent\Model $object, string $key)
 * @method static array getMetaBoxes()
 *
 * @see \Botble\Base\Supports\MetaBox
 */
class MetaBox extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MetaBoxSupport::class;
    }
}
