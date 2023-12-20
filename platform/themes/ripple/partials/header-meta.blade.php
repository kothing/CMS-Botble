{!! BaseHelper::googleFonts('https://fonts.googleapis.com/css2?family=' . urlencode(theme_option('primary_font', 'Roboto')) . '&display=swap') !!}

<style>
    :root {
        --color-1st: {{ theme_option('primary_color', '#AF0F26') }};
        --primary-font: '{{ theme_option('primary_font', 'Roboto') }}', sans-serif;
    }
</style>

@php
    Theme::asset()->container('footer')->remove('simple-slider-js');
@endphp
