@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="form-content-area">
    @if ($showStart)
        {!! Form::open(Arr::except($formOptions, ['template'])) !!}
    @endif

    @php do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel()) @endphp
    <div class="max-width-1200">
        <div class="flexbox-grid no-pd-none">

            <div class="flexbox-content">
                @php do_action(BASE_ACTION_META_BOXES, 'main', $form->getModel()) @endphp
                <div class="widget meta-boxes">
                    <div class="widget-title">
                        <h4>
                            <span>{{ trans('core/base::forms.basic_info_title') }}</span>
                        </h4>
                    </div>
                    <div class="widget-body">
                        @if ($showFields)
                            @foreach ($fields as $key => $field)
                                @if ($field->getName() == $form->getBreakFieldPoint())
                                    @break
                                @else
                                    @unset($fields[$key])
                                @endif
                                @if (!in_array($field->getName(), $exclude))
                                    {!! $field->render() !!}
                                    @if (defined('BASE_FILTER_SLUG_AREA') && $field->getName() == SlugHelper::getColumnNameToGenerateSlug($form->getModel()))
                                        {!! apply_filters(BASE_FILTER_SLUG_AREA, null, $form->getModel()) !!}
                                    @endif
                                @endif
                            @endforeach
                        @endif
                        </div>
             </div>
                @foreach ($form->getMetaBoxes() as $key => $metaBox)
                    {!! $form->getMetaBox($key) !!}
                @endforeach

                @php do_action(BASE_ACTION_META_BOXES, 'advanced', $form->getModel()) @endphp
        </div>
        <div class="flexbox-content flexbox-right">
            <div class="d-flex flex-column-reverse flex-md-column">
                <div class="form-actions-wrapper">
                    {!! $form->getActionButtons() !!}
                </div>
                <div class="form-side-meta-boxes">
                    @php do_action(BASE_ACTION_META_BOXES, 'top', $form->getModel()) @endphp

                    @foreach ($fields as $field)
                        @if (!in_array($field->getName(), $exclude))
                            @if ($field->getType() == 'hidden')
                                {!! $field->render() !!}
                            @else
                                <div class="widget meta-boxes">
                                    <div class="widget-title">
                                        <h4>{!! Form::customLabel($field->getName(), $field->getOption('label'), $field->getOption('label_attr')) !!}</h4>
                                    </div>
                                    <div class="widget-body">
                                        {!! $field->render([], false) !!}
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endforeach

                    @php do_action(BASE_ACTION_META_BOXES, 'side', $form->getModel()) @endphp
                </div>
            </div>
        </div>
    </div>

    @if ($showEnd)
        {!! Form::close() !!}
    @endif
@stop

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

