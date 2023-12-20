@if (empty($widgetSetting) || $widgetSetting->status == 1)
    <div class="col">
        <a class="dashboard-stat dashboard-stat-v2 text-white" style="background-color: {{ $widget->color }};" href="{{ $widget->route }}">
            <div class="visual">
                <i class="{{ $widget->icon }}" style="opacity: .1;"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{{ $widget->statsTotal }}">0</span>
                </div>
                <div class="desc">{{ $widget->title }}</div>
            </div>
        </a>
    </div>
@endif
