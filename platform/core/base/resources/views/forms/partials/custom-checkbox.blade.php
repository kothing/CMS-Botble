@if (sizeof($values = (array) $values) > 1)
    <div class="mt-checkbox-list">
@endif
    @foreach ($values as $value)
        <label class="mb-2">
            <input type="checkbox"
                value="{{ $value[1] ?? '' }}"
                @checked($value[3] ?? false)
                @disabled($value[4] ?? false)
                name="{{ $value[0] ?? '' }}">
            {!! BaseHelper::clean($value[2] ?? '') !!}
        </label>
    @endforeach
@if (sizeof($values) > 1)
    </div>
@endif
