<div id="select-post-language">
    <table class="select-language-table">
        <tbody>
            <tr>
                <td class="active-language">
                    {!! language_flag($currentLanguage->lang_flag, $currentLanguage->lang_name) !!}
                </td>
                <td class="translation-column">
                    <div class="ui-select-wrapper">
                        <select name="language" id="post_lang_choice" class="ui-select">
                            @foreach($languages as $language)
                                @if (!array_key_exists(json_encode([$language->lang_code]), $related))
                                    <option value="{{ $language->lang_code }}" @if ($language->lang_code == $currentLanguage->lang_code) selected="selected" @endif data-flag="{{ $language->lang_flag }}">{{ $language->lang_name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <svg class="svg-next-icon svg-next-icon-size-16">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 16l-4-4h8l-4 4zm0-12L6 8h8l-4-4z"></path></svg>
                        </svg>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

@if (count($languages) > 1)
    <div><strong>{{ trans('plugins/language::language.translations') }}</strong>
        <div id="list-others-language">
            @foreach($languages as $language)
                @if ($language->lang_code != $currentLanguage->lang_code)
                    {!! language_flag($language->lang_flag, $language->lang_name) !!}
                    @if (array_key_exists($language->lang_code, $related))
                        <a href="{{ Route::has($route['edit']) ? route($route['edit'], $related[$language->lang_code]) : '#' }}"> {{ $language->lang_name }} <i class="fa fa-edit"></i></a>
                        <br>
                    @else
                        <a href="{{ Route::has($route['create']) ? route($route['create']) : '#' }}?{{ http_build_query(array_merge($queryParams, [Language::refLangKey() => $language->lang_code])) }}"> {{ $language->lang_name }} <i class="fa fa-plus"></i></a>
                        <br>
                    @endif
                @endif
            @endforeach
        </div>
    </div>

    <input type="hidden" id="lang_meta_created_from" name="ref_from" value="{{ Language::getRefFrom() }}">
    <input type="hidden" id="reference_id" value="{{ $queryParams['ref_from'] }}">
    <input type="hidden" id="reference_type" value="{{ $args[1] }}">
    <input type="hidden" id="route_create" value="{{ Route::has($route['create']) ? route($route['create']) : '#' }}">
    <input type="hidden" id="route_edit" value="{{ Route::has($route['edit']) ? route($route['edit'], $queryParams['ref_from']) : '#' }}">
    <input type="hidden" id="language_flag_path" value="{{ BASE_LANGUAGE_FLAG_PATH }}">

    <div data-change-language-route="{{ route('languages.change.item.language') }}"></div>

    <x-core-base::modal
        id="confirm-change-language-modal"
        :title="trans('plugins/language::language.confirm-change-language')"
        type="warning"
        button-id="confirm-change-language-button"
        :button-label="trans('plugins/language::language.confirm-change-language-btn')"
    >
        {!! BaseHelper::clean(trans('plugins/language::language.confirm-change-language-message')) !!}
    </x-core-base::modal>
@endif

@push('header')
    <meta name="{{ Language::refFromKey() }}" content="{{ $queryParams['ref_from'] }}">
    <meta name="{{ Language::refLangKey() }}" content="{{ $currentLanguage->lang_code }}">
@endpush
