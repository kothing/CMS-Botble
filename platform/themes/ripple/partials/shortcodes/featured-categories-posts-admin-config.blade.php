<div class="form-group">
    <label class="control-label">{{ __('Title') }}</label>
    {!! Form::input('text', 'title', Arr::get($attributes, 'title'), ['class' => 'form-control']) !!}
</div>

<div class="form-group mb-3">
    <label class="control-label">{{ __('Category') }}</label>
    {!! Form::customSelect('category_id', ['' => __('All')] + $categories, Arr::get($attributes, 'category_id')) !!}
</div>
