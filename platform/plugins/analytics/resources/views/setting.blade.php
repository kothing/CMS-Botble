<x-core-setting::section
    :title="trans('plugins/analytics::analytics.settings.title')"
    :description="trans('plugins/analytics::analytics.settings.description')"
>
    <x-core-setting::text-input
        name="google_analytics"
        :label="trans('plugins/analytics::analytics.settings.google_tag_id')"
        :value="setting('google_analytics')"
        :placeholder="trans('plugins/analytics::analytics.settings.google_tag_id_placeholder')"
        helper-text="<a href='https://support.google.com/analytics/answer/9539598#find-G-ID' target='_blank'>https://support.google.com/analytics/answer/9539598#find-G-ID</a>"
        data-counter="120"
    />

    <x-core-setting::text-input
        name="analytics_property_id"
        :label="trans('plugins/analytics::analytics.settings.analytics_property_id')"
        :value="setting('analytics_property_id')"
        :placeholder="trans('plugins/analytics::analytics.settings.analytics_property_id_description')"
        data-counter="9"
        helper-text="<a href='https://developers.google.com/analytics/devguides/reporting/data/v1/property-id' target='_blank'>https://developers.google.com/analytics/devguides/reporting/data/v1/property-id</a>"
    />

    @if (! BaseHelper::hasDemoModeEnabled())
        <x-core-setting::form-group>
            <label class="text-title-field" for="analytics_service_account_credentials">{{ trans('plugins/analytics::analytics.settings.json_credential') }}</label>
            <textarea class="next-input form-control" name="analytics_service_account_credentials" id="analytics_service_account_credentials" rows="5" placeholder="{{ trans('plugins/analytics::analytics.settings.json_credential_description') }}">{{ setting('analytics_service_account_credentials') }}</textarea>
            {!! Form::helper(Html::link('https://github.com/akki-io/laravel-google-analytics/wiki/2.-Configure-Google-Service-Account-&-Google-Analytics', attributes: ['target' => '_blank'])) !!}
        </x-core-setting::form-group>
    @endif
</x-core-setting::section>
