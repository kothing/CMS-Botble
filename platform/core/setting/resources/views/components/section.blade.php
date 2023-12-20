@props([
    'title' => null,
    'description' => null,
    'preFooter' => null,
    'extraDescription' => null,
])

<div class="flexbox-annotated-section">
    <div class="flexbox-annotated-section-annotation">
        @if($title)
            <div class="annotated-section-title pd-all-20">
                <h2>{!! $title !!}</h2>
            </div>
        @endif
        @if($description)
            <div class="annotated-section-description pd-all-20 p-none-t">
                <p class="color-note">{!! $description !!}</p>
            </div>
        @endif

        {!! $extraDescription ?: null !!}
    </div>

    <div class="flexbox-annotated-section-content">
        <div class="wrapper-content pd-all-20">
            {{ $slot }}
        </div>

        {!! $preFooter ?: null !!}
    </div>
</div>
