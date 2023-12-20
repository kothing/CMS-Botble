@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    @if ($showStart)
        {!! Form::open(Arr::except($formOptions, ['template'])) !!}
    @endif

    @php do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel()) @endphp
    <div class="row">
        <div class="col-md-9">
            <div class="tabbable-custom">
                <ul class="nav nav-tabs ">
                    <li class="nav-item">
                        <a href="#tab_detail" class="nav-link active" data-bs-toggle="tab">{{ trans('core/base::tabs.detail') }} </a>
                    </li>
                    {!! apply_filters(BASE_FILTER_REGISTER_CONTENT_TABS, null, $form->getModel()) !!}
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_detail">
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
                        <div class="clearfix"></div>
                    </div>
                    {!! apply_filters(BASE_FILTER_REGISTER_CONTENT_TAB_INSIDE, null, $form->getModel()) !!}
                </div>
            </div>

            @foreach ($form->getMetaBoxes() as $key => $metaBox)
                {!! $form->getMetaBox($key) !!}
            @endforeach

            @php do_action(BASE_ACTION_META_BOXES, 'advanced', $form->getModel()) @endphp

            @yield('form_main_end')
        </div>
        <div class="col-md-3 right-sidebar d-flex flex-column-reverse flex-md-column">
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

    @if ($showEnd)
        {!! Form::close() !!}
    @endif

    @yield('form_end')
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

