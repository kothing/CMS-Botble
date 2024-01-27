@php
    $featuredPosts = get_featured_posts($limit ?: 3, ['slugable', 'categories', 'categories.slugable']);
@endphp

<div class="bg-grey pb-30">
    <div class="container pt-30">
        <div class="featured-slider-3 position-relative">
            <div class="slider-3-arrow-cover"></div>
            <div class="featured-slider-3-items">
                @foreach($featuredPosts as $post)
                    <div class="slider-single overflow-hidden border-radius-10">
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
                                            <a class="text-white" href="{{ $post->url }}" tabindex="{{ $loop->index }}">{{ $post->name }}</a>
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
        </div>
    </div>
</div>
