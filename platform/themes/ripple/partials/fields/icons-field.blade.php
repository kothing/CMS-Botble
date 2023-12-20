@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
        <div {!! $options['wrapperAttrs'] !!}>
    @endif
@endif

@if ($showLabel && $options['label'] !== false && $options['label_show'])
    {!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
@endif

@if ($showField)
    @php
        Arr::set($options['attr'], 'class', Arr::get($options['attr'], 'class') . ' icon-select');
        $data = [$options['value'] => $options['value']];
        Arr::set($options['attr'], 'data-empty-value', Arr::get($options, 'empty_value'));
        Arr::set($options['attr'], 'data-check-initialized', 'true');
    @endphp
    {!! Form::customSelect($name, $data, $options['value'], $options['attr']) !!}
    @include('core/base::forms.partials.help-block')
@endif

@include('core/base::forms.partials.errors')

@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
        </div>
    @endif
@endif
