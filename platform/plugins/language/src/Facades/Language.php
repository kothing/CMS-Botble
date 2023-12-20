<?php

namespace Botble\Language\Facades;

use Botble\Language\LanguageManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getSupportedLocales()
 * @method static void setSupportedLocales(array $locales)
 * @method static \Illuminate\Support\Collection|array getActiveLanguage(array $select = ['*'])
 * @method static string|null getDefaultLocale()
 * @method static void setDefaultLocale()
 * @method static string getHiddenLanguageText()
 * @method static array getRelatedLanguageItem(string|int $id, string|null $uniqueKey)
 * @method static string localizeURL(string|null $url = null, null $locale = null)
 * @method static string getLocalizedURL(string|bool $locale = null, string|false $url = null, array $attributes = [], $forceDefaultLocation = true)
 * @method static string|bool|null getCurrentLocale()
 * @method static bool checkLocaleInSupportedLocales(string|bool|null $locale)
 * @method static string|bool getURLFromRouteNameTranslated(string|false|null $locale, string $transKeyName, array $attributes = [], bool $forceDefaultLocation = false)
 * @method static bool hideDefaultLocaleInURL()
 * @method static string createUrlFromUri(string|null $uri)
 * @method static string getNonLocalizedURL(string|false $url = null)
 * @method static string|null getLocaleFromMapping(string|null $locale)
 * @method static array getLocalesMapping()
 * @method static string|null getInversedLocaleFromMapping(string|null $locale)
 * @method static string|null getCurrentLocaleName()
 * @method static mixed getCurrentLocaleRTL()
 * @method static string|null getCurrentLocaleCode()
 * @method static string|null getLocaleByLocaleCode(string $localeCode)
 * @method static void setCurrentAdminLocale(string|null $code)
 * @method static string|null getCurrentAdminLocale()
 * @method static string|null getCurrentAdminLocaleCode()
 * @method static string|null getDefaultLocaleCode()
 * @method static string|null getCurrentLocaleFlag()
 * @method static array getSupportedLanguagesKeys()
 * @method static void setRouteName(string $routeName)
 * @method static string transRoute(string $routeName)
 * @method static string|bool getRouteNameFromAPath(string $path)
 * @method static void setBaseUrl(string $url)
 * @method static bool saveLanguage(string $screen, \Illuminate\Http\Request $request, \Botble\Base\Models\BaseModel|false|null $data)
 * @method static \Botble\Language\Models\Language|\Botble\Base\Models\BaseModel|\Illuminate\Database\Eloquent\Model|null getDefaultLanguage(array $select = ['*'])
 * @method static array supportedModels()
 * @method static bool deleteLanguage(string $screen, \Botble\Base\Models\BaseModel|false|null $data)
 * @method static \Botble\Language\LanguageManager registerModule(array|string $model)
 * @method static string|null setLocale(string|null $locale = null)
 * @method static string|null getForcedLocale()
 * @method static bool useAcceptLanguageHeader()
 * @method static \Botble\Language\LanguageManager setSwitcherURLs(array $urls)
 * @method static string|null getSwitcherUrl(string $localeCode, string $languageCode)
 * @method static string getSerializedTranslatedRoutes()
 * @method static void setSerializedTranslatedRoutes(string|null $serializedRoutes)
 * @method static string setRoutesCachePath()
 * @method static string refLangKey()
 * @method static string refFromKey()
 * @method static string|null getRefLang()
 * @method static string|int|null getRefFrom()
 * @method static void initModelRelations()
 *
 * @see \Botble\Language\LanguageManager
 */
class Language extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LanguageManager::class;
    }
}
