<?php

namespace Botble\Theme\Facades;

use Botble\Theme\Theme as BaseTheme;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Botble\Theme\Theme layout(string $layout)
 * @method static \Botble\Theme\Theme uses(string|null $theme = null)
 * @method static \Botble\Theme\Theme theme(string|null $theme = null)
 * @method static bool exists(string|null $theme)
 * @method static string path(string|null $forceThemeName = null)
 * @method static mixed|null getConfig(string|null $key = null)
 * @method static string getThemeNamespace(string $path = '')
 * @method static string getThemeName()
 * @method static \Botble\Theme\Theme setThemeName(string $theme)
 * @method static string getPublicThemeName()
 * @method static void fire(string $event, callable|object|array|string|null $args)
 * @method static \Botble\Theme\Breadcrumb breadcrumb()
 * @method static \Botble\Theme\Theme append(string $region, string $value)
 * @method static \Botble\Theme\Theme set(string $region, mixed|null $value)
 * @method static \Botble\Theme\Theme prepend(string $region, string $value)
 * @method static mixed bind(string $variable, callable|array|string|null $callback = null)
 * @method static bool binded(string $variable)
 * @method static mixed share(string $key, $value)
 * @method static string|null partialWithLayout(string $view, array $args = [])
 * @method static string getLayoutName()
 * @method static string|null partial(string $view, array $args = [])
 * @method static string|null loadPartial(string $view, string $partialDir, array $args)
 * @method static string|null watchPartial(string $view, array $args = [])
 * @method static void partialComposer(array|string $view, \Closure $callback, string|null $layout = null)
 * @method static void composer(array|string $view, \Closure $callback, string|null $layout = null)
 * @method static string|null place(string $region, string|null $default = null)
 * @method static mixed get(string $region, string|null $default = null)
 * @method static bool has(string $region)
 * @method static string|null content()
 * @method static \Botble\Theme\Asset|\Botble\Theme\AssetContainer asset()
 * @method static \Botble\Theme\Theme ofWithLayout(string $view, array $args = [])
 * @method static \Botble\Theme\Theme of(string $view, array $args = [])
 * @method static mixed scope(string $view, array $args = [], $default = null)
 * @method static \Botble\Theme\Theme setUpContent(string $view, array $args = [])
 * @method static \Botble\Theme\Theme load(string $view, array $args = [])
 * @method static array getContentArguments()
 * @method static mixed getContentArgument(string $key, $default = null)
 * @method static bool hasContentArgument(string $key)
 * @method static string|null location(bool $realPath = false)
 * @method static \Illuminate\Http\Response render(int $statusCode = 200)
 * @method static string header()
 * @method static string footer()
 * @method static void routes()
 * @method static string loadView(string $view)
 * @method static string getStyleIntegrationPath()
 * @method static \Botble\Theme\Theme fireEventGlobalAssets()
 * @method static string getThemeScreenshot(string $theme)
 *
 * @see \Botble\Theme\Theme
 */
class Theme extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaseTheme::class;
    }
}
