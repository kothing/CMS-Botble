<link rel="alternate" href="{{ Language::getLocalizedURL(Language::getDefaultLocale(), url()->current(), [], false) }}" hreflang="x-default" />

@if (!empty($urls))
    @foreach($urls as $item)
        <link rel="alternate" href="{{ $item['url'] }}" hreflang="{{ $item['lang_code'] }}" />
    @endforeach
@else
    @foreach(Language::getSupportedLocales() as $localeCode => $properties)
        <link rel="alternate" href="{{ Language::getLocalizedURL($localeCode, url()->current(), [], false) }}" hreflang="{{ $localeCode }}" />
    @endforeach
@endif
