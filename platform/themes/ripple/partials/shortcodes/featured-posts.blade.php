@if ($posts->isNotEmpty())
    <section class="section pt-50 pb-50 bg-lightgray">
        <div class="container">
            <div class="post-group post-group--hero">
                @foreach ($posts as $post)
                    @if ($loop->first)
                        <div class="post-group__left">
                            <article class="post post__inside post__inside--feature">
                                <div class="post__thumbnail">
                                    <img src="{{ RvMedia::getImageUrl($post->image, 'featured', false, RvMedia::getDefaultImage()) }}" alt="{{ $post->name }}" loading="lazy"><a href="{{ $post->url }}" title="{{ $post->name }}" class="post__overlay"></a>
                                </div>
                                <header class="post__header">
                                    <h3 class="post__title"><a href="{{ $post->url }}">{{ $post->name }}</a></h3>
                                    <div class="post__meta"><span class="post-category"><i class="ion-cube"></i>
                                            @if (!$post->categories->isEmpty())<a href="{{ $post->first_category->url }}">{{ $post->first_category->name }}</a>@endif
                                    </span>
                                        <span class="created_at"><i class="ion-clock"></i>{{ $post->created_at->translatedFormat('M d Y') }}</span>
                                        @if ($post->author->name)
                                            <span class="post-author"><i class="ion-android-person"></i><span>{{ $post->author->name }}</span></span>
                                        @endif
                                    </div>
                                </header>
                            </article>
                        </div>
                        <div class="post-group__right">
                            @else
                                <div class="post-group__item">
                                    <article class="post post__inside post__inside--feature post__inside--feature-small">
                                        <div class="post__thumbnail"><img src="{{ RvMedia::getImageUrl($post->image, 'medium', false, RvMedia::getDefaultImage()) }}" alt="{{ $post->name }}" loading="lazy"><a href="{{ $post->url }}" title="{{ $post->name }}" class="post__overlay"></a></div>
                                        <header class="post__header">
                                            <h3 class="post__title"><a href="{{ $post->url }}">{{ $post->name }}</a></h3>
                                        </header>
                                    </article>
                                </div>
                                @if ($loop->last)
                        </div>
                    @endif
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif
