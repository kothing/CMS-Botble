@props([
    'name',
    'label' => null,
    'value' => null,
    'type' => 'text',
    'helperText' => null,
])

<x-core-setting::form-group>
    @if($label)
        <label for="{{ $name }}" class="text-title-field">{{ $label }}</label>
    @endif

    <input type="{{ $type }}" {{ $attributes->merge([
        'class' => 'form-control next-input' . ($errors->has($name) ? ' is-invalid' : ''),
        'name' => $name,
        'id' => $name,
    ]) }} value="{{ old($name) && ! is_array(old($name)) ? old($name) : $value }}">

    @if($helperText)
        {{ Form::helper($helperText) }}
    @endif

    {{ $slot }}
</x-core-setting::form-group>
