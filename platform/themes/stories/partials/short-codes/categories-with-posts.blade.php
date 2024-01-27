<div class="site-bottom pt-50 pb-50">
    <div class="container">
        <div class="row">
            @foreach($categories as $category)
                <div class="col-lg-{{ 12 / count($categories) }} col-md-{{ 12 / (count($categories) + 1) }}">
                    <div class="sidebar-widget widget-latest-posts mb-30 wow fadeInUp  animated" style="visibility: visible; animation-name: fadeInUp;">
                        <div class="widget-header-2 position-relative mb-30">
                            <h5 class="mt-5 mb-30">{{ $category->name }}</h5>
                        </div>
                        <div class="post-block-list post-module-1">
                            <ul class="list-post">
                                @foreach($category->posts as $post)
                                    <li class="mb-30">
                                        <div class="d-flex hover-up-2 transition-normal">
                                            <div class="post-thumb post-thumb-80 d-flex mr-15 border-radius-5 img-hover-scale overflow-hidden">
                                                <a class="color-white" href="{{ $post->url }}">
                                                    <img src="{{ RvMedia::getImageUrl($post->image) }}" alt="{{ $post->name }}" loading="lazy">
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
                </div>
            @endforeach
        </div>
    </div>
    <!--container-->
</div>
