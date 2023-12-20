<div class="widget meta-boxes">
    <div class="widget-title">
        <h4><span>{{ $title ?? trans('core/base::forms.image') }}</span></h4>
    </div>
    <div class="widget-body">
        {!! Form::mediaImage($name ?? 'image', $value) !!}
        {!! Form::error($name ?? 'image', $errors) !!}
    </div>
</div>
