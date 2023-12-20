<div class="form-group">
    <label class="control-label">{{ __('Limit') }}</label>
    {!! Form::input('number', 'limit', Arr::get($attributes, 'limit', 5), ['class' => 'form-control']) !!}
</div>
