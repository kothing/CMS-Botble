@php
    $field['options'] = config('core.base.general.google_fonts', []);

    $customGoogleFonts = config('core.base.general.custom_google_fonts');

    if ($customGoogleFonts) {
        $field['options'] = array_merge($field['options'], explode(',', $customGoogleFonts));
    }

    $customFonts = config('core.base.general.custom_fonts');

    if ($customFonts) {
        $field['options'] = array_merge($field['options'], explode(',', $customFonts));
    }
@endphp

{!! Form::customSelect($name, ['' => __('-- Select --')] + array_combine($field['options'], $field['options']), $selected, ['class' => 'select2_google_fonts_picker']) !!}

@once
    @push('footer')
        {!! Html::style(BaseHelper::getGoogleFontsURL() . '/css?family=' . implode('|', array_map('urlencode', array_filter($field['options']))) . '&display=swap') !!}
    @endpush
@endonce

