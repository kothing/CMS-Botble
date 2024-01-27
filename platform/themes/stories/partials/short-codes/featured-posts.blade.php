@php
    $featuredPosts = get_featured_posts($limit ?: 6, ['slugable', 'categories', 'categories.slugable']);
@endphp

<div class="container">
    <div class="hot-tags pt-30 pb-30 font-small align-self-center">
        <div class="widget-header-3">
            <div class="row align-self-center">
                <div class="col-md-4 align-self-center">
                    <h5 class="widget-title">{!! BaseHelper::clean($title) !!}</h5>
                </div>
                <div class="col-md-8 text-md-right font-small align-self-center">
                    <p class="d-inline-block mr-5 mb-0"><i class="elegant-icon  icon_tag_alt mr-5 text-muted"></i>{{ __('Hot tags') }}:</p>
                    <ul class="list-inline d-inline-block tags">
                        @foreach(get_popular_tags(4) as $tag)
                            <li class="list-inline-item"><a href="{{ $tag->url }}"># {{ $tag->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="loop-grid mb-30">
        <div class="row">
            @if ($featuredPosts->count() > 1)
                <div class="col-lg-8 mb-30">
                    <div class="carausel-post-1 hover-up border-radius-10 overflow-hidden transition-normal position-relative">
                        <div class="arrow-cover"></div>
                        <div class="slide-fade">
                            @foreach($featuredPosts->take(2) as $post)
                                <div class="position-relative post-thumb">
                                    <div class="thumb-overlay img-hover-slide position-relative" style="background-image: url({{ RvMedia::getImageUrl($post->image, null, false, RvMedia::getDefaultImage()) }})">
                                        <a class="img-link" href="{{ $post->url }}"></a>
                                        <span class="top-left-icon bg-warning"><i class="elegant-icon icon_star_alt"></i></span>
                                        <div class="post-content-overlay text-white ml-30 mr-30 pb-30">
                                            <div class="entry-meta meta-0 font-small mb-20">
                                                @foreach($post->categories as $category)
                                                    <a href="{{ $category->url }}"><span class="post-cat {{ random_color() }} text-uppercase">{{ $category->name }}</span></a>
                                                @endforeach
                                            </div>
                                            <h3 class="post-title font-weight-900 mb-20">
                                                <a class="text-white" href="{{ $post->url }}">{{ $post->name }}</a>
                                            </h3>
                                            <div class="entry-meta meta-1 font-small text-white mt-10 pr-5 pl-5">
                                                <span class="post-on">{{ $post->created_at->diffForHumans() }}</span>
                                                <span class="hit-count has-dot">{{ number_format($post->views) }} {{ __('views') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @foreach($featuredPosts->skip(2)->take(1) as $post)
                <article class="col-lg-4 col-md-6 mb-30">
                    {!! Theme::partial('components.post-card', compact('post')) !!}
                </article>
            @endforeach

                @foreach($featuredPosts->skip(3) as $post)
                <article class="col-lg-4 col-md-6 mb-30 wow fadeInUp animated" data-wow-delay="0.{{ $loop->index * 2 }}s">
                    {!! Theme::partial('components.post-card', compact('post')) !!}
                </article>
            @endforeach
        </div>
    </div>
</div>
