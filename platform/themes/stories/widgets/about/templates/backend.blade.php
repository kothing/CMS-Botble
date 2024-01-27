<div class="form-group">
    <label for="widget-name">{{ trans('core/base::forms.name') }}</label>
    <input type="text" class="form-control" name="name" value="{{ $config['name'] }}">
</div>
<div class="form-group">
    <label for="widget-name">{{ trans('core/base::forms.description') }}</label>
    <textarea rows="3" class="form-control" name="description">{{ $config['description'] }}</textarea>
</div>
<div class="form-group">
    <label for="widget-name">{{ trans('core/base::forms.image') }}</label>
    {!! Form::mediaImage('image', $config['image']) !!}
</div>
