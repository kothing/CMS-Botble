<x-core-setting::section
    :title="trans('packages/theme::theme.settings.title')"
    :description="trans('packages/theme::theme.settings.description')"
>
    <x-core-setting::on-off
        name="enable_cache_site_map"
        :label="trans('core/setting::setting.general.enable_cache_site_map')"
        :value="setting('enable_cache_site_map', true)"
        class="setting-selection-option"
        data-target="#cache-sitemap-settings"
    />

    <div id="cache-sitemap-settings" @class(['mb-4 border rounded-top rounded-bottom p-3 bg-light', 'd-none' => ! setting('enable_cache_site_map', true)])>
        <x-core-setting::text-input
            name="cache_time_site_map"
            type="number"
            :label="trans('core/setting::setting.general.cache_time_site_map')"
            :value="setting('cache_time_site_map', 60)"
        />
    </div>

    <x-core-setting::checkbox
        name="show_admin_bar"
        :label="trans('packages/theme::theme.show_admin_bar')"
        :checked="setting('show_admin_bar', true)"
    />

    <x-core-setting::on-off
        name="redirect_404_to_homepage"
        :label="trans('packages/theme::theme.settings.redirect_404_to_homepage')"
        :value="setting('redirect_404_to_homepage', false)"
    />

    <x-core-setting::on-off
        name="show_theme_guideline_link"
        :label="trans('packages/theme::theme.settings.show_guidelines')"
        :value="setting('show_theme_guideline_link', false)"
    />
</x-core-setting::section>
