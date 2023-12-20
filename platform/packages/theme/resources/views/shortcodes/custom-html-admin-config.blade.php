<div class="form-group">
    <label class="control-label">{{  __('Content') }}</label>
    {!! Form::textarea('content', $content, ['class' => 'form-control', 'data-shortcode-attribute' => 'content', 'rows' => 3, 'placeholder' => __('HTML code')]) !!}
</div>
