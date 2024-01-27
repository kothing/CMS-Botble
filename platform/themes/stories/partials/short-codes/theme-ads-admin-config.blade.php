@for ($i = 1; $i < 5; $i++)
    <div class="form-group">
        <label class="control-label">Ad {{ $i }}</label>
        <select name="key_{{ $i }}" class="form-control">
            <option value="">{{ __('-- select --') }}</option>
            @foreach($ads as $ad)
                <option value="{{ $ad->key }}" @if ($ad->key == Arr::get($attributes, 'key_' . $i)) selected @endif>{{ $ad->name }}</option>
            @endforeach
        </select>
    </div>
@endfor
