<div class="site-bottom pt-50 pb-50">
    <div class="container">
        <div class="sidebar-widget widget-latest-posts mb-30 mt-20 wow fadeInUp animated">
            <div class="widget-header-2 position-relative mb-30">
                <h5 class="mt-5 mb-30">{!! BaseHelper::clean($title) !!}</h5>
            </div>
            <div class="carousel-slider" data-items="{{ (int)$shortcode->number_items_to_show > 0 ? (int)$shortcode->number_items_to_show : 3 }}">
                @foreach (get_featured_categories((int)$shortcode->limit > 0 ? (int)$shortcode->limit : 10, ['slugable', 'image']) as $category)
                    <div class="carousel-slider-item d-flex bg-grey has-border p-25 hover-up-2 transition-normal border-radius-5">
                        <div class="post-thumb post-thumb-64 d-flex mr-15 border-radius-5 img-hover-scale overflow-hidden">
                            <a class="color-white" href="{{ $category->url }}">
                                @if ($category->image && count($category->image->meta_value) > 0)
                                    <img src="{{ RvMedia::getImageUrl($category->image->meta_value[0], 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $category->name }}" loading="lazy">
                                @endif
                            </a>
                        </div>
                        <div class="post-content media-body">
                            <h6> <a href="{{ $category->url }}">{{ $category->name }}</a> </h6>
                            <p class="text-muted font-small">{{ Str::limit($category->description, 65) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
