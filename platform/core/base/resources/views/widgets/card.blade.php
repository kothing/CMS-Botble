<div @class(['mb-3', 'widget-item', 'col-md-' . $columns => $columns]) id="{{ $id . '-parent' }}">
    <div class="h-100 bg-white-opacity position-relative">
        {!! $content !!}
        @if($hasChart)
            <div id="{{ $id }}" class="position-absolute fixed-bottom"></div>
        @endif
    </div>

    @if($hasChart)
        @include('core/base::widgets.partials.chart-script')
    @endif
</div>
