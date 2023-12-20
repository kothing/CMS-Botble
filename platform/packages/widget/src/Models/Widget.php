<?php

namespace Botble\Widget\Models;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Models\BaseModel;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\App;

class Widget extends BaseModel
{
    protected $table = 'widgets';

    protected $fillable = [
        'widget_id',
        'sidebar_id',
        'theme',
        'position',
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    protected function position(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value >= 0 && $value < 127 ? $value : (int)substr($value, -1)
        );
    }

    public static function getThemeName(
        string $locale = null,
        string $defaultLocale = null,
        string $theme = null
    ): string {
        if (! $theme) {
            $theme = Theme::getThemeName();
        }

        if ($refLang = BaseHelper::stringify(request()->input('ref_lang'))) {
            $locale = $refLang;
        }

        if (! $defaultLocale) {
            $defaultLocale = App::getLocale();
        }

        return (! $locale || $locale == $defaultLocale) ? $theme : ($theme . '-' . ltrim($locale, '-'));
    }
}
