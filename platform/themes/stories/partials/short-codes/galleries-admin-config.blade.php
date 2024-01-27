<div class="form-group">
    <label class="control-label">{{ __('Title') }}</label>
    <input name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control" />
</div>

<div class="form-group">
    <label class="control-label">{{ __('Limit') }}</label>
    {!! Form::input('text', 'limit', Arr::get($attributes, 'limit', 8), ['class' => 'form-control']) !!}
</div>
