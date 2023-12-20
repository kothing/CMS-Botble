@if (Arr::get($metaBox, 'before_wrapper'))
    {!! Arr::get($metaBox, 'before_wrapper') !!}
@endif

@if (Arr::get($metaBox, 'wrap', true))
    <div class="widget meta-boxes" {!! Html::attributes(Arr::get($metaBox, 'attributes', [])) !!}>
        <div class="widget-title">
            <h4>
                <span> {{ Arr::get($metaBox, 'title') }}</span>
            </h4>
        </div>
        <div class="widget-body">
            {!! Arr::get($metaBox, 'content') !!}
        </div>
    </div>
@else
    {!! Arr::get($metaBox, 'content') !!}
@endif

@if (Arr::get($metaBox, 'after_wrapper'))
    {!! Arr::get($metaBox, 'after_wrapper') !!}
@endif
