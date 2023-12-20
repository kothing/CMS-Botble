<span class="admin-list-language-chooser">
    <span class="d-inline-block me-1">{{ trans('plugins/language::language.translations') }}:</span>
    @foreach (($languages ?? Language::getActiveLanguage()) as $language)
        @continue($language->lang_code === Language::getCurrentAdminLocaleCode())

        <span>
            <span>{!! language_flag($language->lang_flag, $language->lang_name) !!}</span>
            <a href="{{ route($route, array_merge($params ?? [], $language->lang_code === Language::getDefaultLocaleCode() ? [] : [Language::refLangKey() => $language->lang_code])) }}" class="d-inline-block ms-1">{{ $language->lang_name }}</a>
        </span>&nbsp;
    @endforeach
    <input type="hidden" name="{{ Language::refLangKey() }}" value="{{ Language::getRefLang() }}">
</span>
