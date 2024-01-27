@if (is_plugin_active('gallery'))
    <div class="sidebar-widget widget_instagram wow fadeInUp animated">
        <div class="widget-header-1 position-relative mb-30">
            <h5 class="mt-5 mb-30">{{ $config['name'] }}</h5>
        </div>
        <div class="instagram-gellay">
            <ul class="insta-feed">
                @foreach (get_galleries($config['number_display']) as $gallery)
                    <li>
                        <a href="{{ $gallery->url }}" data-animate="zoomIn" data-duration="1.5s" data-delay="0.1s"><img class="border-radius-5" src="{{ RvMedia::getImageUrl($gallery->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $gallery->name }}"></a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
