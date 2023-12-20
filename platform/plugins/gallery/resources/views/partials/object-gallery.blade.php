<div style="position: relative; margin-bottom: 10px;">
    <div class="c-content-media-2-slider" data-slider="owl" data-single-item="true" data-auto-play="4000">
        @if ($category != null)
            <div class="c-content-label">{{ $category }}</div>
        @endif
        <div class="owl-carousel owl-theme c-theme owl-single">
            @foreach ($galleries as $image)
                @if ($image)
                    <div class="item">
                        <div class="c-content-media-2 c-bg-img-center" style="background-image: url('{{ RvMedia::getImageUrl(Arr::get($image, 'img')) }}'); min-height: 380px;">
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
