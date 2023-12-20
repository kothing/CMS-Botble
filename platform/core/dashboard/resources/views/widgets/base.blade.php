@if (empty($widgetSetting) || $widgetSetting->status == 1)
    <div class="{{ $widget->column }} col-12 widget_item" id="{{ $widget->name }}" data-url="{{ $widget->route }}">
        <div class="portlet light bordered portlet-no-padding @if ($widget->hasLoadCallback) widget-load-has-callback @endif">
            <div class="portlet-title">
                <div class="caption">
                    <i class="{{ $widget->icon }} font-dark fw-bold" data-bs-toggle="tooltip" title="{{ $widget->title }}"></i>
                    <span class="caption-subject font-dark d-none d-xl-inline">{{ $widget->title }}</span>
                </div>
                @include('core/dashboard::partials.tools')
            </div>
            <div class="portlet-body @if ($widget->isEqualHeight) equal-height @endif widget-content {{ $widget->bodyClass }} {{ Arr::get($settings, 'state') }}"></div>
        </div>
    </div>
@endif
