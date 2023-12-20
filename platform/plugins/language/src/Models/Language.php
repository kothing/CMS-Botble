<?php

namespace Botble\Language\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\Setting\Facades\Setting;
use Botble\Theme\Facades\ThemeOption;
use Botble\Widget\Models\Widget;
use Exception;

class Language extends BaseModel
{
    public $timestamps = false;

    protected $primaryKey = 'lang_id';

    protected $table = 'languages';

    protected $fillable = [
        'lang_name',
        'lang_locale',
        'lang_is_default',
        'lang_code',
        'lang_is_rtl',
        'lang_flag',
        'lang_order',
    ];

    protected $casts = [
        'lang_name' => SafeContent::class,
        'lang_locale' => SafeContent::class,
        'lang_code' => SafeContent::class,
    ];

    protected static function booted(): void
    {
        self::deleted(function (Language $language) {
            $defaultLanguage = self::where('lang_is_default', 1)->first();

            if (empty($defaultLanguage) && self::exists()) {
                $defaultLanguage = self::first();
                $defaultLanguage->lang_is_default = 1;
                $defaultLanguage->save();
            }

            $meta = LanguageMeta::where('lang_meta_code', $language->lang_code)->get();

            try {
                foreach ($meta as $item) {
                    $item->reference()->delete();
                }
            } catch (Exception $exception) {
                info($exception->getMessage());
            }

            LanguageMeta::where('lang_meta_code', $language->lang_code)->delete();

            Setting::newQuery()->where('key', 'LIKE', ThemeOption::getOptionKey('%', $language->lang_code))->delete();
            Widget::where('theme', 'LIKE', Widget::getThemeName($language->lang_code))->delete();
        });
    }
}
