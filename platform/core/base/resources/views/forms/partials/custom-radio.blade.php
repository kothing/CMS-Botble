@php
$values = (array)$values;
@endphp
@if (count($values) > 1) <div class="mt-radio-list"> @endif
    @foreach($values as $line)
        @php
            $value = $line[0] ?? '';
            $label = $line[1] ?? '';
            $disabled = $line[2] ?? '';
        @endphp
        <label class="me-2">
            <input type="radio"
                   value="{{ $value }}"
                   {{ (string)$selected === (string)$value ? 'checked' : '' }}
                   name="{{ $name }}" {{ $disabled ? 'disabled' : '' }}>
            {{ $label }}
        </label>
    @endforeach
@if (count($values) > 1) </div> @endif
