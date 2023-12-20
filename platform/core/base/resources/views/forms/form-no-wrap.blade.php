@if ($showStart)
    {!! Form::open(Arr::except($formOptions, ['template'])) !!}
@endif

@php do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel()) @endphp

@if ($showFields)
    @foreach ($fields as $field)
        @if (!in_array($field->getName(), $exclude))
            {!! $field->render() !!}
            @if (defined('BASE_FILTER_SLUG_AREA') && $field->getName() == SlugHelper::getColumnNameToGenerateSlug($form->getModel()))
                {!! apply_filters(BASE_FILTER_SLUG_AREA, null, $form->getModel()) !!}
            @endif
        @endif
    @endforeach
@endif
<div class="clearfix"></div>

@foreach ($form->getMetaBoxes() as $key => $metaBox)
    {!! $form->getMetaBox($key) !!}
@endforeach

@php do_action(BASE_ACTION_META_BOXES, 'top', $form->getModel()) @endphp
@php do_action(BASE_ACTION_META_BOXES, 'side', $form->getModel()) @endphp
@php do_action(BASE_ACTION_META_BOXES, 'advanced', $form->getModel()) @endphp

{!! $form->getActionButtons() !!}

@if ($showEnd)
    {!! Form::close() !!}
@endif

@if ($form->getValidatorClass())
    @if ($form->isUseInlineJs())
        {!! Assets::scriptToHtml('jquery') !!}
        {!! Assets::scriptToHtml('form-validation') !!}
        {!! $form->renderValidatorJs() !!}
    @else
        @push('footer')
            {!! $form->renderValidatorJs() !!}
        @endpush
    @endif
@endif

