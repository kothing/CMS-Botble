<?php

namespace Botble\SeoHelper\Facades;

use Botble\SeoHelper\SeoHelper as BaseSeoHelper;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Botble\SeoHelper\SeoHelper setSeoMeta(\Botble\SeoHelper\Contracts\SeoMetaContract $seoMeta)
 * @method static \Botble\SeoHelper\SeoHelper setSeoOpenGraph(\Botble\SeoHelper\Contracts\SeoOpenGraphContract $seoOpenGraph)
 * @method static \Botble\SeoHelper\SeoHelper setSeoTwitter(\Botble\SeoHelper\Contracts\SeoTwitterContract $seoTwitter)
 * @method static \Botble\SeoHelper\Contracts\SeoOpenGraphContract openGraph()
 * @method static \Botble\SeoHelper\SeoHelper setTitle(string|null $title, string|null $siteName = null, string|null $separator = null)
 * @method static \Botble\SeoHelper\Contracts\SeoMetaContract meta()
 * @method static \Botble\SeoHelper\Contracts\SeoTwitterContract twitter()
 * @method static string|null getTitle()
 * @method static string|null getDescription()
 * @method static \Botble\SeoHelper\SeoHelper setDescription($description)
 * @method static mixed render()
 * @method static bool saveMetaData(string $screen, \Illuminate\Http\Request $request, \Botble\Base\Models\BaseModel $object)
 * @method static bool deleteMetaData(string $screen, \Botble\Base\Models\BaseModel $object)
 * @method static \Botble\SeoHelper\SeoHelper registerModule(array|string $model)
 *
 * @see \Botble\SeoHelper\SeoHelper
 */
class SeoHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaseSeoHelper::class;
    }
}
