<?php

namespace Botble\Base\Facades;

use Botble\Base\Helpers\BaseHelper as BaseHelperSupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string formatTime(\Carbon\Carbon $timestamp, string|null $format = 'j M Y H:i')
 * @method static string|null formatDate(string|null $date, string|null $format = null)
 * @method static string|null formatDateTime(string|null $date, string|null $format = null)
 * @method static string humanFilesize(float $bytes, int $precision = 2)
 * @method static mixed getFileData(string $file, bool $convertToArray = true)
 * @method static bool saveFileData(string $path, array|string|null $data, bool $json = true)
 * @method static string jsonEncodePrettify(array|string|null $data)
 * @method static array scanFolder(string $path, array $ignoreFiles = [])
 * @method static string getAdminPrefix()
 * @method static string getAdminMasterLayoutTemplate()
 * @method static string siteLanguageDirection()
 * @method static bool isRtlEnabled()
 * @method static string adminLanguageDirection()
 * @method static bool isHomepage(string|int|null $pageId = null)
 * @method static string|null getHomepageId()
 * @method static bool isJoined(\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query, string $table)
 * @method static array availableRichEditors()
 * @method static string getRichEditor()
 * @method static string|null removeQueryStringVars(string|null $url, array|string $key)
 * @method static string cleanEditorContent(string|null $value)
 * @method static string getPhoneValidationRule()
 * @method static \Illuminate\Support\Collection sortSearchResults(\Illuminate\Support\Collection|array $collection, string $searchTerms, string $column)
 * @method static array getDateFormats()
 * @method static array|string|null clean(array|string|null $dirty, array|string|null $config = null)
 * @method static \Illuminate\Support\HtmlString html(array|string|null $dirty, array|string|null $config = null)
 * @method static string hexToRgba(string $color, float $opacity = 1)
 * @method static array hexToRgb(string $color)
 * @method static \Botble\Base\Helpers\BaseHelper iniSet(string $key, string|int|null $value)
 * @method static \Botble\Base\Helpers\BaseHelper maximumExecutionTimeAndMemoryLimit()
 * @method static array|string|null removeSpecialCharacters(string|null $string)
 * @method static string getInputValueFromQueryString(string $name)
 * @method static string|null cleanShortcodes(string|null $content)
 * @method static string|null stringify($content)
 * @method static string getGoogleFontsURL()
 * @method static mixed googleFonts(string $font, bool $inline = true)
 * @method static bool hasDemoModeEnabled()
 *
 * @see \Botble\Base\Helpers\BaseHelper
 */
class BaseHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaseHelperSupport::class;
    }
}
