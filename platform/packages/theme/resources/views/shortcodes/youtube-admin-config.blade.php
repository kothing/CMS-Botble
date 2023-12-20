<div class="form-group mb-3">
    <label class="control-label">{{ __('Youtube URL') }}</label>
    {!! Form::input('text', 'url', $content, ['class' => 'form-control', 'placeholder' => 'https://www.youtube.com/watch?v=FN7ALfpGxiI', 'data-shortcode-attribute' => 'content']) !!}
</div>
