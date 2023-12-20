@php
    Assets::addScriptsDirectly('vendor/core/core/base/js/repeater-field.js')
    ->usingVueJS();

    $group = '';
    foreach ($fields as $key => $field) {
        $item = Form::hidden($name . '[__key__][' . $key . '][key]', $field['attributes']['name']);
        $field['attributes']['name'] = $name . '[__key__][' . $key . '][value]';
        $field['attributes']['options']['id'] = 'repeater_field_' . md5($field['attributes']['name']) . '__key__';
        Arr::set($field, 'label_attr.for', $field['attributes']['options']['id']);
        $item .= Form::customLabel(Arr::get($field, 'attr.name'), $field['label'], Arr::get($field, 'label_attr')) .
        call_user_func_array([Form::class, $field['type']], array_values($field['attributes']));
        $group .= '<div class="form-group mb-3">' . $item . '</div>';
    }

    $defaultFields = ['<div class="repeater-item-group form-group mb-3">' . $group . '</div>'];

    $values = (array)json_decode($value ?: '[]', true);

    $added = [];

    if (count($values) > 0) {
        for ($i = 0; $i < count($values); $i++) {
            $group = '';
            foreach ($fields as $key => $field) {
                $item = Form::hidden($name . '[' . $i . '][' . $key . '][key]', $field['attributes']['name']);
                $field['attributes']['name'] = $name . '[' . $i . '][' . $key . '][value]';
                $field['attributes']['value'] = Arr::get($values, $i . '.' . $key . '.value');
                $field['attributes']['options']['id'] = 'repeater_field_' . md5($field['attributes']['name']);
                Arr::set($field, 'label_attr.for', $field['attributes']['options']['id']);
                $item .= Form::customLabel(Arr::get($field, 'attr.name'), $field['label'], Arr::get($field, 'label_attr')) .
                call_user_func_array([Form::class, $field['type']], array_values($field['attributes']));

                $group .= '<div class="form-group mb-3">' . $item . '</div>';
            }

            $added[] = '<div class="repeater-item-group form-group mb-3">' . $group . '</div>';
        }
    }
@endphp

<input type="hidden" name="{{ $name }}" value="[]">
<repeater-component :fields="{{ json_encode($defaultFields) }}" :added="{{ json_encode($added) }}"></repeater-component>
