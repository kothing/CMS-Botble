<div class="form-group">
    <label class="control-label">{{ __('Title') }}</label>
    <input name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control" />
</div>

<div class="form-group">
    <label class="control-label">{{ __('Number of categories to show (on desktop screen)') }}</label>
    {!! Form::customSelect('number_items_to_show', array_combine([2, 3, 4, 5, 6], [2, 3, 4, 5, 6]), Arr::get($attributes, 'number_items_to_show', 3)) !!}
</div>

<div class="form-group">
    <label class="control-label">{{ __('Limit') }}</label>
    {!! Form::input('text', 'limit', Arr::get($attributes, 'limit', 10), ['class' => 'form-control']) !!}
</div>
