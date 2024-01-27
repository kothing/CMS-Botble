@if (count($ads))
    <div class="mt-30 mb-30">
        <div class="container">
            <div class="row">
                @for($i = 0; $i < count($ads); $i++)
                    <div class="col-lg-{{ 12 / count($ads) }}">
                        {!! $ads[$i] !!}
                    </div>
                @endfor
            </div>
        </div>
    </div>
@endif
