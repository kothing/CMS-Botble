<?php

namespace Botble\Captcha\Providers;

use Botble\Base\Supports\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(BASE_FILTER_AFTER_SETTING_CONTENT, [$this, 'addSettings'], 299);

        add_filter(THEME_FRONT_HEADER, [$this, 'addHeaderMeta'], 299);

        add_filter('cms_settings_validation_rules', [$this, 'addSettingRules'], 299);
    }

    public function addSettingRules(array $rules): array
    {
        return array_merge($rules, [
            'enable_captcha' => 'nullable|in:0,1',
            'captcha_type' => 'nullable|in:v2,v3|required_if:enable_captcha,1',
            'captcha_hide_badge' => 'nullable|in:0,1|required_if:enable_captcha,1',
            'captcha_site_key' => 'nullable|string|required_if:enable_captcha,1',
            'captcha_secret' => 'nullable|string|required_if:enable_captcha,1',
        ]);
    }

    public function addSettings(string|null $data = null): string
    {
        return $data . view('plugins/captcha::setting')->render();
    }

    public function addHeaderMeta(string|null $html): string
    {
        return $html . view('plugins/captcha::header-meta')->render();
    }
}
