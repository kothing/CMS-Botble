<input type="hidden" name="language" value="{{ $currentLanguage?->lang_code }}">
<div id="list-others-language">
    @foreach($languages as $language)
        @continue(! $currentLanguage || $language->lang_code === $currentLanguage->lang_code)

        {!! language_flag($language->lang_flag, $language->lang_name) !!}
        <a href="{{ Route::has($route['edit']) ? Request::url() . ($language->lang_code != Language::getDefaultLocaleCode() ? '?' . Language::refLangKey() .'=' . $language->lang_code : null) : '#' }}" target="_blank" class="d-inline-block ms-1">{{ $language->lang_name }} <i class="fas fa-external-link-alt"></i></a>
        <br>
    @endforeach
</div>

@push('header')
    <meta name="{{ Language::refFromKey() }}" content="{{ (!empty($args[0]) && $args[0]->id ? $args[0]->id : 0) }}">
    <meta name="{{ Language::refLangKey() }}" content="{{ $currentLanguage?->lang_code }}">
@endpush
