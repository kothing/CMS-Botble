@foreach ($widgetAreas as $item)
    @if (class_exists($item->widget_id, false))
        @php($widget = new $item->widget_id)
        <li data-id="{{ $widget->getId() }}" data-position="{{ $item->position }}">
            <div class="widget-handle">
                <p class="widget-name">{{ $widget->getConfig()['name'] }} <span class="text-end"><i class="fa fa-caret-down"></i></span></p>
            </div>
            <div class="widget-content">
                <form method="post">
                    <input type="hidden" name="id" value="{{ $widget->getId() }}">
                    {!! $widget->form($item->sidebar_id, $item->position) !!}
                    <div class="widget-control-actions">
                        <div class="float-start">
                            <button class="btn btn-danger widget-control-delete">{{ trans('packages/widget::widget.delete') }}</button>
                        </div>
                        <div class="float-end text-end">
                            <button class="btn btn-primary widget_save">{{ trans('core/base::forms.save_and_continue') }}</button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </form>
            </div>
        </li>
    @endif
@endforeach
