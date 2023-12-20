<x-core-setting::radio
    :$name
    :$label
    :helperText="$helperText ?? null"
    :value="$value ?? 1"
    :options="[
        1 => trans('core/setting::setting.general.yes'),
        0 => trans('core/setting::setting.general.no'),
    ]"
    {{ $attributes }}
/>
