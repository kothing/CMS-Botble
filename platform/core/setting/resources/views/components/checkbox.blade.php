@props([
    'name',
    'label' => null,
    'helperText' => null,
    'value' => null,
    'checked' => false,
    'helperText' => null,
])

<x-core-setting::form-group>
    <input type="hidden" name="{{ $name }}" value="{{ (int)! ($value !== null ? $value : 1) }}">
    <label>
        <input type="checkbox" value="{{ $value !== null ? $value : 1 }}" @checked($checked ?? $value) name="{{ $name }}" id="{{ $name }}" {{ $attributes }}>
        {{ $label }}
    </label>

    @if($helperText)
        {{ Form::helper($helperText) }}
    @endif
</x-core-setting::form-group>
