<div id="{{ $chart->getElementId() }}" style="width: 100%; display: block; min-height: 300px;"></div>

@push('footer')
    <script type="text/javascript">
        jQuery(function () {
            `use strict`;

            Morris.{{ $chart->chartType }}(
                {!! $chart->toJSON() !!}
            );
        });
    </script>
@endpush

@if ($chart->isUseInlineJs())
    {!! Assets::styleToHtml('morris') !!}
    {!! Assets::scriptToHtml('raphael') !!}
    {!! Assets::scriptToHtml('morris') !!}

    <script type="text/javascript">
        jQuery(function () {
            `use strict`;

            Morris.{{ $chart->chartType }}(
               {!! $chart->toJSON() !!}
            );
        });
    </script>
@else
    @push('footer')
        <script type="text/javascript">
            jQuery(function () {
                `use strict`;

                Morris.{{ $chart->chartType }}(
                   {!! $chart->toJSON() !!}
                );
            });
        </script>
    @endpush
@endif
