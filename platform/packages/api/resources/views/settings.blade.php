@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="max-width-1200">
        {!! Form::open(['route' => ['api.settings.update']]) !!}
            <x-core-setting::section
                :title="trans('packages/api::api.setting_title')"
                :description="trans('packages/api::api.setting_description')"
            >
                <x-core-setting::on-off
                    name="api_enabled"
                    :label="trans('packages/api::api.api_enabled')"
                    :value="ApiHelper::enabled()"
                />
            </x-core-setting::section>

            <div class="flexbox-annotated-section" style="border: none">
                <div class="flexbox-annotated-section-annotation">&nbsp;</div>
                <div class="flexbox-annotated-section-content">
                    <button class="btn btn-info" type="submit">{{ trans('packages/api::api.save_settings') }}</button>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
@endsection
