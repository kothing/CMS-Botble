<div class="form-group">
    <label for="time_to_read" class="control-label">{{ __('Time to read (minute)') }}</label>
    {!! Form::number('time_to_read', $timeToRead, ['class' => 'form-control', 'id' => 'time_to_read']) !!}
</div>

<div class="form-group">
    <label for="layout" class="control-label">{{ __('Layout') }}</label>
    {!! Form::customSelect('layout', get_blog_single_layouts(), $layout, ['class' => 'form-control', 'id' => 'layout']) !!}
</div>
