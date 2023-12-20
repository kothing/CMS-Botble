@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="widget meta-boxes">
        <div class="widget-title">
            <h4>&nbsp; {{ trans('plugins/translation::translation.theme-translations') }}</h4>
        </div>
        <div class="widget-body box-translation" v-pre>
            @if (count($groups) > 0 && $group)
                <div class="row">
                    <div class="col-md-6">
                        <p>{{ trans('plugins/translation::translation.translate_from') }}
                            <strong class="text-info">{{ $defaultLanguage ? $defaultLanguage['name'] : 'en' }}</strong>
                            {{ trans('plugins/translation::translation.to') }}
                            <strong class="text-info">{{ $group['name'] }}</strong>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="text-end">
                            @include('plugins/translation::partials.list-theme-languages-to-translate', compact('groups', 'group'))
                        </div>
                    </div>
                </div>
                <p class="note note-warning" style="margin-bottom: 65px;">{{ trans('plugins/translation::translation.theme_translations_instruction') }}</p>

                {!! apply_filters('translation_theme_translation_header', null, $groups, $group) !!}

                {!! $translationTable->renderTable() !!}
                <br>

                {!! Form::open(['role' => 'form', 'route' => 'translations.theme-translations.post', 'method' => 'POST']) !!}
                    <input type="hidden" name="locale" value="{{ $group['locale'] }}">
                    <div class="form-group mb-3">
                        <button type="submit" class="btn btn-info button-save-theme-translations">{{ trans('core/base::forms.save') }}</button>
                    </div>
                {!! Form::close() !!}
            @else
                <p class="text-warning">{{ trans('plugins/translation::translation.no_other_languages') }}</p>
            @endif
        </div>
        <div class="clearfix"></div>
    </div>
@stop
