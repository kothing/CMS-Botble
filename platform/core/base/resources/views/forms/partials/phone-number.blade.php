@php
    if (is_in_admin()) {
        Assets::addStylesDirectly('vendor/core/base/libraries/intl-tel-input/css/intlTelInput.min.css')
            ->addScriptsDirectly([
                'vendor/core/base/libraries/intl-tel-input/js/intlTelInput.min.js',
                'vendor/core/base/js/phone-number-field.js',
            ]);
    } else {
        Theme::asset()->container('footer')->add('intlTelInput-css', 'vendor/core/base/libraries/intl-tel-input/css/intlTelInput.min.css');
        Theme::asset()->container('footer')->add('intlTelInput-js', 'vendor/core/base/libraries/intl-tel-input/js/intlTelInput.min.js');
        Theme::asset()->container('footer')->add('phone-number-field-js', 'vendor/core/base/js/phone-number-field.js');
    }
@endphp

{!! Form::text($name, $value, array_merge_recursive($attributes, ['class' => 'js-phone-number-mask form-control'])) !!}
