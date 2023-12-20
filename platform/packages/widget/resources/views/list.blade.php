@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    @php do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), WIDGET_MANAGER_MODULE_SCREEN_NAME) @endphp
    <div class="widget-main" id="wrap-widgets">
        <div class="row row-cols-2">
            <div class="col">
                <h2>{{ trans('packages/widget::widget.available') }}</h2>
                <p>{{ trans('packages/widget::widget.instruction') }}</p>
                <ul id="wrap-widget-1" class="row row-cols-1 row-cols-md-2 g-2">
                    @foreach (Widget::getWidgets() as $widget)
                        <li data-id="{{ $widget->getId() }}" class="col">
                            <div class="widget-handle">
                                <p class="widget-name">
                                    {{ $widget->getConfig()['name'] }}
                                    <span class="text-end">
                                        <i class="fa fa-caret-up"></i>
                                    </span>
                                </p>
                            </div>
                            <div class="widget-content">
                                <form method="post">
                                    <input type="hidden" name="id" value="{{ $widget->getId() }}">
                                    {!! $widget->form() !!}
                                    <div class="widget-control-actions">
                                        <div class="float-start">
                                            <button class="btn btn-danger widget-control-delete">{{ trans('packages/widget::widget.delete') }}</button>
                                        </div>
                                        <div class="float-end text-end">
                                            <button class="btn btn-primary widget_save">{{ trans('core/base::forms.save_and_continue') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="widget-description">
                                <p class="small">{{ $widget->getConfig()['description'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="col" id="added-widget">
                {!! apply_filters(WIDGET_TOP_META_BOXES, null, WIDGET_MANAGER_MODULE_SCREEN_NAME) !!}
                <div class="row row-cols-1 row-cols-md-2">
                    @foreach (WidgetGroup::getGroups() as $group)
                        <div class="col sidebar-item" data-id="{{ $group->getId() }}">
                            <div class="sidebar-area">
                                <div class="sidebar-header">
                                    <h3 class="text-break position-relative pe-3" role="button">
                                        {{ $group->getName() }}
                                        <span class="position-absolute end-0 top-0 me-1">
                                            <i class="fa fa-caret-down"></i>
                                        </span>
                                    </h3>
                                    <p>{{ $group->getDescription() }}</p>
                                </div>
                                <ul id="wrap-widget-{{ $loop->index + 2 }}">
                                    @include('packages/widget::item', ['widgetAreas' => $group->getWidgets()])
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@stop

@push('footer')
    <script>
        'use strict';
        var BWidget = BWidget || {};
        BWidget.routes = {
            'delete': '{{ route('widgets.destroy', ['ref_lang' => BaseHelper::stringify(request()->input('ref_lang'))]) }}',
            'save_widgets_sidebar': '{{ route('widgets.save_widgets_sidebar', ['ref_lang' => BaseHelper::stringify(request()->input('ref_lang'))]) }}'
        };
    </script>
@endpush
