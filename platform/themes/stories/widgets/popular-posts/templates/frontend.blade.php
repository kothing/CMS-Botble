@if (is_plugin_active('blog'))
    <div class="sidebar-widget widget-latest-posts mb-50 wow fadeInUp animated">
        <div class="widget-header-1 position-relative mb-30">
            <h5 class="mt-5 mb-30">{{ $config['name'] }}</h5>
        </div>
        <div class="post-block-list post-module-1">
            <ul class="list-post">
                @foreach(get_popular_posts($config['number_display']) as $post)
                    <li class="mb-30 wow fadeInUp animated">
                        <div class="d-flex bg-white has-border p-25 hover-up transition-normal border-radius-5">
                            <div class="post-content media-body">
                                <h6 class="post-title mb-15 text-limit-2-row font-medium"><a href="{{ $post->url }}">{{ $post->name }}</a></h6>
                                <div class="entry-meta meta-1 float-left font-x-small text-uppercase">
                                    <span class="post-on">{{ $post->created_at->translatedFormat('M d, Y') }}</span>
                                    <span class="post-by has-dot">{{ number_format($post->views) }} {{ __('views') }}</span>
                                </div>
                            </div>
                            <div class="post-thumb post-thumb-80 d-flex ml-15 border-radius-5 img-hover-scale overflow-hidden">
                                <a class="color-white" href="{{ $post->url }}">
                                    <img src="{{ RvMedia::getImageUrl($post->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $post->name }}" loading="lazy">
                                </a>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
