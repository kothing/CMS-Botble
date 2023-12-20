@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="max-width-1200">
        {!! Form::open(['route' => ['setting.email.template.store']]) !!}
            <input type="hidden" name="module" value="{{ $pluginData['name'] }}">
            <input type="hidden" name="template_file" value="{{ $pluginData['template_file'] }}">

            <x-core-setting::section
                :title="trans('core/setting::setting.email.title')"
                :description="trans('core/setting::setting.email.description')"
            >
                @if ($emailSubject)
                    <input type="hidden" name="email_subject_key" value="{{ get_setting_email_subject_key($pluginData['type'], $pluginData['name'], $pluginData['template_file']) }}">

                    <x-core-setting::text-input
                        name="email_subject"
                        :label="trans('core/setting::setting.email.subject')"
                        :value="$emailSubject"
                        data-counter="300"
                    />
                @endif

                <x-core-setting::form-group>
                    <label class="text-title-field" for="email_content">{{ trans('core/setting::setting.email.content') }}</label>
                    <div class="d-inline-flex mb-3">
                        <div class="dropdown me-2">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fa fa-code"></i> {{ __('Variables') }}
                            </button>
                            <ul class="dropdown-menu">
                                @foreach(EmailHandler::getVariables($pluginData['type'], $pluginData['name'], $pluginData['template_file']) as $key => $label)
                                    <li>
                                        <a href="#" class="js-select-mail-variable" data-key="{{ $key }}">
                                            <span class="text-danger">{{ $key }}</span>: {{ trans($label) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fa fa-code"></i> {{ __('Functions') }}
                            </button>
                            <ul class="dropdown-menu">
                                @foreach(EmailHandler::getFunctions() as $key => $function)
                                    <li>
                                        <a href="#" class="js-select-mail-function" data-key="{{ $key }}" data-sample="{{ $function['sample'] }}">
                                            <span class="text-danger">{{ $key }}</span>: {{ trans($function['label']) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <textarea id="mail-template-editor" name="email_content" class="form-control" style="overflow-y:scroll; height: 500px;">{{ $emailContent }}</textarea>
                    {{ Form::helper(__('Learn more about Twig template: :url', ['url' => Html::link('https://twig.symfony.com/doc/3.x/', null, ['target' => '_blank'])])) }}
                </x-core-setting::form-group>

                <x-slot:pre-footer>
                    <div class="mt-3">
                        {!! apply_filters('setting_email_template_meta_boxes', null, request()->route()->parameters()) !!}
                    </div>
                </x-slot:pre-footer>
            </x-core-setting::section>

            <div class="flexbox-annotated-section" style="border: none">
                <div class="flexbox-annotated-section-annotation">&nbsp;</div>
                <div class="flexbox-annotated-section-content">
                    <a href="{{ route('settings.email') }}" class="btn btn-secondary">{{ trans('core/setting::setting.email.back') }}</a>
                    <a href="{{ route('setting.email.preview', ['type' => $pluginData['type'], 'module' => $pluginData['name'], 'template' => $pluginData['template_file']]) }}" target="_blank" class="btn btn-success">
                        {{ trans('core/setting::setting.preview') }}
                        <i class="fa fa-external-link"></i>
                    </a>
                    <a class="btn btn-warning btn-trigger-reset-to-default" data-target="{{ route('setting.email.template.reset-to-default', ['ref_lang' => BaseHelper::stringify(request()->input('ref_lang'))]) }}">{{ trans('core/setting::setting.email.reset_to_default') }}</a>
                    <button class="btn btn-info" type="submit" name="submit">{{ trans('core/setting::setting.save_settings') }}</button>
                </div>
            </div>
        {!! Form::close() !!}
    </div>

    <x-core-base::modal
        id="reset-template-to-default-modal"
        :title="trans('core/setting::setting.email.confirm_reset')"
        type="info"
        button-id="reset-template-to-default-button"
        :button-label="trans('core/setting::setting.email.continue')"
    >
        {!! trans('core/setting::setting.email.confirm_message') !!}
    </x-core-base::modal>
@endsection
