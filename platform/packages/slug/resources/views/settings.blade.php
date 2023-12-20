@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="max-width-1200">
        {!! Form::open(['route' => ['slug.settings']]) !!}
        <x-core-setting::section
            :title="trans('packages/slug::slug.settings.title')"
            :description="trans('packages/slug::slug.settings.description')">
            <x-slot name="extraDescription">
                @if (config('packages.slug.general.enable_slug_translator'))
                    <div class="pd-all-20">
                        <p>{{ trans('packages/slug::slug.settings.available_variables') }}:</p>
                        @foreach (SlugHelper::getTranslator()->getVariables() as $key => $item)
                            <p>
                                <code class="p-1">
                                    <strong>{{ $key }}</strong> - {{ $item['label'] }}
                                </code>
                            </p>
                        @endforeach
                    </div>
                @endif
            </x-slot>

            @foreach (SlugHelper::supportedModels() as $model => $name)
                <x-core-setting::text-input
                    :name="SlugHelper::getPermalinkSettingKey($model)"
                    :label="trans('packages/slug::slug.prefix_for', ['name' => $name])"
                    :value="ltrim(rtrim(old(SlugHelper::getPermalinkSettingKey($model), SlugHelper::getPrefix($model, '', false)), '/'), '/')"
                    @class([
                        'form-control',
                        'is-invalid' => $errors->has(SlugHelper::getPermalinkSettingKey($model)),
                    ])>
                    <input type="hidden" name="{{ SlugHelper::getPermalinkSettingKey($model) }}-model-key"
                           value="{{ $model }}">
                    @error(SlugHelper::getPermalinkSettingKey($model))
                    <span class="invalid-feedback">
                            <strong>{{ $errors->first(SlugHelper::getPermalinkSettingKey($model)) }}</strong>
                        </span>
                    @enderror
                    {!! Form::helper(trans('packages/slug::slug.settings.preview') . ': <a href="javascript:void(0)">' . url((string) SlugHelper::getPrefix($model)) . '/' . Str::slug('your url here') . '</a>') !!}
                </x-core-setting::text-input>
            @endforeach

            <x-core-setting::text-input
                name="public_single_ending_url"
                :label="trans('packages/slug::slug.public_single_ending_url')"
                :value="SlugHelper::getPublicSingleEndingURL()"
                @class([
                    'form-control',
                    'is-invalid' => $errors->has('public_single_ending_url'),
                ])>
                {!! Form::helper(trans('packages/slug::slug.settings.preview') . ': <a href="javascript:void(0)">' . url(Str::slug('your url here') . SlugHelper::getPublicSingleEndingURL()) . '</a>') !!}
            </x-core-setting::text-input>

            <hr>

            <x-core-setting::on-off
                name="slug_turn_off_automatic_url_translation_into_latin"
                :label="trans('packages/slug::slug.settings.turn_off_automatic_url_translation_into_latin')"
                :value="SlugHelper::turnOffAutomaticUrlTranslationIntoLatin()" />

        </x-core-setting::section>

        <div class="flexbox-annotated-section" style="border: none">
            <div class="flexbox-annotated-section-annotation">&nbsp;</div>
            <div class="flexbox-annotated-section-content">
                <button class="btn btn-info" type="submit">{{ trans('core/setting::setting.save_settings') }}</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
