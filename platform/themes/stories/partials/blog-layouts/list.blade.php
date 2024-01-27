<div class="post-module-3">
    <div class="loop-list loop-list-style-1">
        @foreach($posts as $post)
            <article class="hover-up-2 transition-normal wow fadeInUp animated">
                <div class="row mb-40 list-style-2">
                    <div class="col-md-4">
                        <div class="post-thumb position-relative border-radius-5">
                            <div class="img-hover-slide border-radius-5 position-relative" style="background-image: url({{ RvMedia::getImageUrl($post->image, 'small', false, RvMedia::getDefaultImage()) }})">
                                <a class="img-link" href="{{ $post->url }}"></a>
                            </div>
                            {!! Theme::partial('social-share-buttons', compact('post')) !!}
                        </div>
                    </div>
                    <div class="col-md-8 align-self-center">
                        <div class="post-content">
                            <div class="entry-meta meta-0 font-small mb-10">
                                @foreach($post->categories as $category)
                                    <a href="{{ $category->url }}"><span class="post-cat {{ random_color() }}">{{ $category->name }}</span></a>
                                @endforeach
                            </div>
                            <h5 class="post-title font-weight-900 mb-20">
                                <a href="{{ $post->url }}">{{ $post->name }}</a>
                            </h5>
                            <div class="entry-meta meta-1 float-left font-x-small text-uppercase">
                                <span class="post-on">{{ $post->created_at->translatedFormat('M d, Y') }}</span>
                                <span class="time-reading has-dot">{{ get_time_to_read($post) }} {{ __('mins read') }}</span>
                                <span class="post-by has-dot">{{ number_format($post->views) }} {{ __('views') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="pagination-area mb-30 wow fadeInUp animated justify-content-start">
        {!! $posts->withQueryString()->onEachSide(1)->links() !!}
    </div>
</div>
