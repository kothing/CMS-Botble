<?php

namespace Botble\Optimize\Providers;

use Botble\Base\Supports\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(BASE_FILTER_AFTER_SETTING_CONTENT, [$this, 'addSetting'], 27);

        add_filter('cms_settings_validation_rules', [$this, 'addSettingRules'], 27);
    }

    public function addSettingRules(array $rules): array
    {
        return array_merge($rules, [
            'optimize_page_speed_enable' => 'nullable|in:0,1',
            'optimize_collapse_white_space' => 'nullable|in:0,1',
            'optimize_elide_attributes' => 'nullable|in:0,1',
            'optimize_inline_css' => 'nullable|in:0,1',
            'optimize_insert_dns_prefetch' => 'nullable|in:0,1',
            'optimize_remove_comments' => 'nullable|in:0,1',
            'optimize_defer_javascript' => 'nullable|in:0,1',
        ]);
    }

    public function addSetting(string|null $data = null): string
    {
        return $data . view('packages/optimize::setting')->render();
    }
}
