@if (function_exists('get_galleries'))
    @php $galleries = get_galleries($shortcode->limit); @endphp
    @if (!$galleries->isEmpty())
        <div class="site-bottom pt-50 pb-50">
            <div class="container">
                <div class="sidebar-widget widget-latest-posts mb-30 mt-20 wow fadeInUp animated">
                    <div class="widget-header-2 position-relative mb-30">
                        <h5 class="mt-5 mb-30">{!! BaseHelper::clean($shortcode->title) !!}</h5>
                    </div>
                    <div class="gallery-wrap">
                        @foreach ($galleries as $gallery)
                            <div class="gallery-item">
                                <div class="img-wrap">
                                    <a href="{{ $gallery->url }}"><img src="{{ RvMedia::getImageUrl($gallery->image, 'medium') }}" alt="{{ $gallery->name }}" loading="lazy"></a>
                                </div>
                                <div class="gallery-detail">
                                    <div class="gallery-title"><a href="{{ $gallery->url }}">{{ $gallery->name }}</a></div>
                                    <div class="gallery-author">{{ trans('plugins/gallery::gallery.by') }} {{ $gallery->user ? $gallery->user->name : '' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    @endif
@endif
