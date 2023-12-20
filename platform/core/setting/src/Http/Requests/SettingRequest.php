<?php

namespace Botble\Setting\Http\Requests;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Language;
use Botble\Support\Http\Requests\Request;
use DateTimeZone;
use Illuminate\Validation\Rule;

class SettingRequest extends Request
{
    public function rules(): array
    {
        return apply_filters('cms_settings_validation_rules', [
            'admin_email' => 'nullable|array',
            'time_zone' => Rule::in(DateTimeZone::listIdentifiers()),
            'locale' => Rule::in(array_keys(Language::getAvailableLocales())),
            'locale_direction' => 'required|in:ltr,rtl',
            'enable_send_error_reporting_via_email' => 'nullable|in:0,1',
            'admin_logo' => 'nullable|string',
            'admin_favicon' => 'nullable|string',
            'login_screen_backgrounds' => 'nullable|array',
            'admin_title' => 'nullable|string|max:255',
            'admin_locale_direction' => 'required|in:ltr,rtl',
            'rich_editor' => ['required', Rule::in(array_keys(BaseHelper::availableRichEditors()))],
            'default_admin_theme' => Rule::in(array_keys(Assets::getThemes())),
            'enable_change_admin_theme' => 'nullable|in:0,1',
            'enable_cache' => 'nullable|in:0,1',
            'cache_time' => 'nullable|integer|min:0',
            'disable_cache_in_the_admin_panel' => 'nullable|in:0,1',
            'cache_admin_menu_enable' => 'nullable|in:0,1',
        ]);
    }
}
