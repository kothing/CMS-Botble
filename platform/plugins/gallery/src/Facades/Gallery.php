<?php

namespace Botble\Gallery\Facades;

use Botble\Gallery\GallerySupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Botble\Gallery\GallerySupport registerModule(array|string $model)
 * @method static array getSupportedModules()
 * @method static \Botble\Gallery\GallerySupport removeModule(array|string $model)
 * @method static void saveGallery(\Illuminate\Http\Request $request, \Illuminate\Database\Eloquent\Model|null $data)
 * @method static bool deleteGallery(\Illuminate\Database\Eloquent\Model|null $data)
 * @method static \Botble\Gallery\GallerySupport registerAssets()
 * @method static string|null getGalleriesPageUrl()
 *
 * @see \Botble\Gallery\GallerySupport
 */
class Gallery extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GallerySupport::class;
    }
}
