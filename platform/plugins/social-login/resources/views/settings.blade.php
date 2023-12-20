@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    {!! Form::open(['route' => ['social-login.settings']]) !!}
        <div class="max-width-1200">
            <x-core-setting::section
                :title="trans('plugins/social-login::social-login.settings.title')"
                :description="trans('plugins/social-login::social-login.settings.description')"
            >
                <x-core-setting::checkbox
                    name="social_login_enable"
                    :label="trans('plugins/social-login::social-login.settings.enable')"
                    :checked="setting('social_login_enable')"
                />
            </x-core-setting::section>

            <div class="wrapper-list-social-login-options" @style(['display:none' => ! SocialService::setting('enable')])>
                @foreach (SocialService::getProviders() as $provider => $item)
                    <x-core-setting::section
                        :title="trans('plugins/social-login::social-login.settings.' . $provider . '.title')"
                        :description="trans('plugins/social-login::social-login.settings.' . $provider . '.description')"
                    >
                        <x-core-setting::checkbox
                            name="social_login_{{ $provider }}_enable"
                            :label="trans('plugins/social-login::social-login.settings.enable')"
                            :checked="SocialService::getProviderEnabled($provider)"
                            class="enable-social-login-option"
                        />

                        <div class="enable-social-login-option-wrapper" @style(['display:none' => ! SocialService::getProviderEnabled($provider)])>
                            @foreach ($item['data'] as $input)
                                @php($isDisabled = in_array(app()->environment(), SocialService::getEnvDisableData()) && in_array($input, Arr::get($item, 'disable', [])))

                                <x-core-setting::text-input
                                    :name="'social_login_' . $provider . '_' . $input"
                                    :label="trans('plugins/social-login::social-login.settings.' . $provider . '.' . $input)"
                                    :value="$isDisabled ? SocialService::getDataDisable($provider . '_' . $input) : setting('social_login_' . $provider . '_' . $input)"
                                    :disabled="$isDisabled"
                                    :readonly="$isDisabled"
                                />
                            @endforeach

                            {{ Form::helper(trans('plugins/social-login::social-login.settings.' . $provider . '.helper', ['callback' => '<code>' . route('auth.social.callback', $provider) . '</code>'])) }}
                        </div>
                    </x-core-setting::section>
                @endforeach
            </div>

            <div class="flexbox-annotated-section" style="border: none">
                <div class="flexbox-annotated-section-annotation">&nbsp;</div>
                <div class="flexbox-annotated-section-content">
                    <button class="btn btn-info" type="submit">{{ trans('core/setting::setting.save_settings') }}</button>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
@endsection
