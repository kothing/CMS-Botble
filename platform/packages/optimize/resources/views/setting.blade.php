<x-core-setting::section
    :title="trans('packages/optimize::optimize.settings.title')"
    :description="trans('packages/optimize::optimize.settings.description')"
>
    <x-core-setting::on-off
        name="optimize_page_speed_enable"
        :label="trans('packages/optimize::optimize.settings.enable')"
        :value="setting('optimize_page_speed_enable', false)"
        class="setting-selection-option"
        data-target="#pagespeed-optimize-settings"
    />

    <div id="pagespeed-optimize-settings" @class(['mb-4 border rounded-top rounded-bottom p-3 bg-light', 'd-none' => ! setting('optimize_page_speed_enable', false)])>
        <x-core-setting::checkbox
            name="optimize_collapse_white_space"
            :label="trans('packages/optimize::optimize.collapse_white_space')"
            :checked="setting('optimize_collapse_white_space', false)"
            :helper-text="trans('packages/optimize::optimize.collapse_white_space_description')"
        />

        <x-core-setting::checkbox
            name="optimize_elide_attributes"
            :label="trans('packages/optimize::optimize.elide_attributes')"
            :checked="setting('optimize_elide_attributes', false)"
            :helper-text="trans('packages/optimize::optimize.elide_attributes_description')"
        />

        <x-core-setting::checkbox
            name="optimize_inline_css"
            :label="trans('packages/optimize::optimize.inline_css')"
            :checked="setting('optimize_inline_css', false)"
            :helper-text="trans('packages/optimize::optimize.inline_css_description')"
        />

        <x-core-setting::checkbox
            name="optimize_insert_dns_prefetch"
            :label="trans('packages/optimize::optimize.insert_dns_prefetch')"
            :checked="setting('optimize_insert_dns_prefetch', false)"
            :helper-text="trans('packages/optimize::optimize.insert_dns_prefetch_description')"
        />

        <x-core-setting::checkbox
            name="optimize_remove_comments"
            :label="trans('packages/optimize::optimize.remove_comments')"
            :checked="setting('optimize_remove_comments', false)"
            :helper-text="trans('packages/optimize::optimize.remove_comments_description')"
        />

        <x-core-setting::checkbox
            name="optimize_remove_quotes"
            :label="trans('packages/optimize::optimize.remove_quotes')"
            :checked="setting('optimize_remove_quotes', false)"
            :helper-text="trans('packages/optimize::optimize.remove_quotes_description')"
        />

        <x-core-setting::checkbox
            name="optimize_defer_javascript"
            :label="trans('packages/optimize::optimize.defer_javascript')"
            :checked="setting('optimize_defer_javascript', false)"
            :helper-text="trans('packages/optimize::optimize.defer_javascript_description')"
        />
    </div>
</x-core-setting::section>
