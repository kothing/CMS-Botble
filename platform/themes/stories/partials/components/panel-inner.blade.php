<!--Categories-->
<div class="sidebar-widget widget_categories mb-50 mt-30">
    <div class="widget-header-2 position-relative">
        <h5 class="mt-5 mb-15">{{ __('Hot topics') }}</h5>
    </div>
    <div class="widget_nav_menu">
        <ul>
            @foreach(get_popular_categories(5) as $category)
                <li class="cat-item cat-item-2"><a href="{{ $category->url }}">{{ $category->name }}</a> <span class="post-count">{{ $category->posts_count }}</span></li>
            @endforeach
        </ul>
    </div>
</div>

<!--Latest-->
<div class="sidebar-widget widget-latest-posts mb-50">
    <div class="widget-header-2 position-relative mb-30">
        <h5 class="mt-5 mb-30">{{ __("Don't miss") }}</h5>
    </div>
    <div class="post-block-list post-module-1 post-module-5">
        <ul class="list-post">
            @foreach(get_latest_posts(3, [], ['slugable']) as $post)
                <li class="mb-30">
                    <div class="d-flex hover-up-2 transition-normal">
                        <div class="post-thumb post-thumb-80 d-flex mr-15 border-radius-5 img-hover-scale overflow-hidden">
                            <a class="color-white" href="{{ $post->url }}">
                                <img src="{{ RvMedia::getImageUrl($post->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $post->name }}" loading="lazy">
                            </a>
                        </div>
                        <div class="post-content media-body">
                            <h6 class="post-title mb-15 text-limit-2-row font-medium"><a href="{{ $post->url }}">{{ $post->name }}</a></h6>
                            <div class="entry-meta meta-1 float-left font-x-small text-uppercase">
                                <span class="post-on">{{ $post->created_at->translatedFormat('M d, Y') }}</span>
                                <span class="post-by has-dot">{{ number_format($post->views) }} {{ __('views') }}</span>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>

@if (AdsManager::locationHasAds('panel-ads'))
    <div class="sidebar-widget">
        <div class="widget-header-2 position-relative mb-30">
            <h5 class="mt-5 mb-30">{{ __('Advertise banner') }}</h5>
        </div>

        {!! display_ad('panel-ads', ['class' => 'border-radius-5']) !!}
    </div>
@endif
