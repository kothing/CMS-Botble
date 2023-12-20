<div @class(['mb-3', 'widget-item', 'col-md-' . $columns => $columns]) id="{{ $id . '-parent' }}">
    <div class="bg-white p-3">
        <h5>{{ $label }}</h5>
        <div id="{{ $id }}"></div>
    </div>
    @include('core/base::widgets.partials.chart-script')
</div>
