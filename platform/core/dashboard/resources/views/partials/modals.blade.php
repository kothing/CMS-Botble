<div id="hide_widget_modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title"><i class="til_img"></i><strong>{{ trans('core/dashboard::dashboard.confirm_hide') }}</strong></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>

            <div class="modal-body with-padding">
                <p>{{ trans('core/dashboard::dashboard.hide_message') }}</p>
            </div>

            <div class="modal-footer">
                <a class="float-start btn btn-danger" href="#" id="hide-widget-confirm-bttn">{{ trans('core/dashboard::dashboard.confirm_hide_btn') }}</a>
                <button type="button" class="float-end btn btn-primary" data-bs-dismiss="modal">{{ trans('core/dashboard::dashboard.cancel_hide_btn') }}</button>
            </div>
        </div>
    </div>
</div>

<div id="manage_widget_modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'dashboard.hide_widgets']) !!}
                <div class="modal-header">
                    <h4 class="modal-title"><i class="til_img"></i><strong>{{ trans('core/dashboard::dashboard.manage_widgets') }}</strong></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">

                    </button>
                </div>
                <div class="modal-body with-padding">
                    @foreach ($widgets as $widget)
                        @php $widget_setting = $widget->settings->first(); @endphp
                        <section class="wrap_{{ $widget->name }}">
                            <i class="{{ $widget->icon ?? 'box_img_sale ' . $widget->name }} @if ($widget_setting && $widget_setting->status == 0) widget_none_color @endif" style="background-color: {{ $widget->color ?? '#7c87b6' }}"></i>
                            <span class="widget_name">{{ $widget->title }}</span>
                            <div class="swc_wrap">
                                {!! Form::onOff('widgets[' . $widget->name . ']', $widget_setting ? $widget_setting->status : true) !!}
                            </div>
                        </section>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('core/base::forms.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('core/base::forms.save') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
