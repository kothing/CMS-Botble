<div class="form-group">
    <label class="control-label">{{ __('Select a category') }}</label>
    <select name="category_id" class="form-control">
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @if ($category->id == Arr::get($attributes, 'category_id')) selected @endif>{{ $category->name }}</option>
        @endforeach
    </select>
</div>
