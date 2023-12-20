<?php

namespace Botble\Base\Facades;

use Botble\Base\Supports\Assets as BaseAssets;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void setConfig(array $config)
 * @method static array getThemes()
 * @method static string renderHeader($lastStyles = [])
 * @method static string renderFooter()
 * @method static \Botble\Base\Supports\Assets usingVueJS()
 * @method static \Botble\Base\Supports\Assets disableVueJS()
 * @method static \Botble\Assets\Assets addScripts(string|array $assets)
 * @method static \Botble\Assets\Assets addStyles(string|array $assets)
 * @method static \Botble\Assets\Assets addStylesDirectly(array|string $assets)
 * @method static \Botble\Assets\Assets addScriptsDirectly(string|array $assets, string $location = 'footer')
 * @method static \Botble\Assets\Assets removeStyles(string|array $assets)
 * @method static \Botble\Assets\Assets removeScripts(string|array $assets)
 * @method static \Botble\Assets\Assets removeItemDirectly(string|array $assets, string|null $location = null)
 * @method static array getScripts(string|null $location = null)
 * @method static array getStyles(array $lastStyles = [])
 * @method static string|null scriptToHtml(string $name)
 * @method static string|null styleToHtml(string $name)
 * @method static string getBuildVersion()
 * @method static \Botble\Assets\HtmlBuilder getHtmlBuilder()
 *
 * @see \Botble\Base\Supports\Assets
 */
class Assets extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaseAssets::class;
    }
}
