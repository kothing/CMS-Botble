@for ($i = 1; $i <= 3; $i++)
    <div class="form-group">
        <label class="control-label">{{ __('Category') }} {{ $i }}</label>
        <select class="form-control"
                name="category_id_{{ $i }}">
            <option value="">{{ __('-- select --') }}</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @if ($category->id == Arr::get($attributes, 'category_id_' . $i)) selected @endif>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
@endfor
