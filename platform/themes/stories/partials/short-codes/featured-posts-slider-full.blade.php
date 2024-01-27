@php
    $featuredPosts = get_featured_posts($limit ?: 5, ['slugable', 'categories', 'categories.slugable']);
@endphp

<div class="featured-slider-2">
    <div class="featured-slider-2-items">
        @foreach($featuredPosts as $post)
            <div class="slider-single">
                <div class="post-thumb position-relative">
                    <div class="thumb-overlay position-relative" style="background-image: url({{ RvMedia::getImageUrl($post->image, null, false, RvMedia::getDefaultImage()) }})">
                        <div class="post-content-overlay">
                            <div class="container">
                                <div class="entry-meta meta-0 font-small mb-20">
                                    @foreach($post->categories as $category)
                                        <a href="{{ $category->url }}" tabindex="{{ $loop->index }}"><span class="post-cat {{ random_color() }} text-uppercase">{{ $category->name }}</span></a>
                                    @endforeach
                                </div>
                                <h1 class="post-title mb-20 font-weight-900 text-white">
                                    <a class="text-white" href="{{ $post->url }}">{{ $post->name }}</a>
                                </h1>
                                <div class="entry-meta meta-1 font-small text-white mt-10 pr-5 pl-5">
                                    <span class="post-on">{{ $post->created_at->diffForHumans() }}</span>
                                    <span class="hit-count has-dot">{{ number_format($post->views) }} {{ __('views') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="container position-relative">
        <div class="arrow-cover color-white"></div>
        <div class="featured-slider-2-nav-cover">
            <div class="featured-slider-2-nav">
                @foreach($featuredPosts as $post)
                    <div class="slider-post-thumb mr-15 mt-20 position-relative">
                        <div class="d-flex hover-up-2 transition-normal">
                            <div class="post-thumb post-thumb-80 d-flex mr-15 border-radius-5">
                                <img class="border-radius-5" src="{{ RvMedia::getImageUrl($post->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $post->name }}">
                            </div>
                            <div class="post-content media-body text-white">
                                <h5 class="post-title mb-15 text-limit-2-row">{{ $post->name }}</h5>
                                <div class="entry-meta meta-1 float-left font-x-small text-uppercase">
                                    <span class="post-on text-white">{{ $post->created_at->diffForHumans() }}</span>
                                    <span class="hit-count has-dot text-white">{{ number_format($post->views) }} {{ __('views') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
