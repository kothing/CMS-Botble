@if (function_exists('get_galleries'))
    @if (! $galleries->isEmpty())
        <div class="gallery-wrap">
            @foreach ($galleries as $gallery)
                <div class="gallery-item">
                    <div class="img-wrap">
                        <a href="{{ $gallery->url }}"><img src="{{ RvMedia::getImageUrl($gallery->image, 'medium') }}" alt="{{ $gallery->name }}"></a>
                    </div>
                    <div class="gallery-detail">
                        <div class="gallery-title"><a href="{{ $gallery->url }}">{{ $gallery->name }}</a></div>
                        <div class="gallery-author">{{ trans('plugins/gallery::gallery.by') }} {{ $gallery->user ? $gallery->user->name : '' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="clear: both"></div>
    @endif
@endif
