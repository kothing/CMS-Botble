<div class="input-group color-picker" data-color="{{ $value ?? '#000' }}">
    {!! Form::text($name, $value ?? '#000', array_merge(['class' => 'form-control'], $attributes)) !!}
    <span class="input-group-text">
    <span class="input-group-text colorpicker-input-addon"><i></i></span>
  </span>
</div>

@once
    @if (request()->ajax())
        <script src="{{ asset('vendor/core/core/base/libraries/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('vendor/core/core/base/libraries/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
    @endif
@endonce

