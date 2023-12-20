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
        $emptyVal = $options['empty_value'] ? ['' => $options['empty_value']] : null;
    @endphp
    {!! Form::customSelect($name, (array)$emptyVal + $options['choices'], $options['selected'], $options['attr'], Arr::get($options, 'optionAttrs', []), Arr::get($options, 'optgroupsAttributes', [])) !!}
    @include('core/base::forms.partials.help-block')
@endif

@include('core/base::forms.partials.errors')

@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
        </div>
    @endif
@endif
