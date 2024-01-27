<div class="post-card-1 border-radius-10 hover-up">
    <div class="post-thumb thumb-overlay img-hover-slide position-relative" style="background-image: url({{ RvMedia::getImageUrl($post->image, null, false, RvMedia::getDefaultImage())}})">
        <a class="img-link" href="{{ $post->url }}"></a>
        {!! Theme::partial('social-share-buttons', compact('post')) !!}
    </div>
    <div class="post-content p-30">
        @if ($post->categories->first())
        <div class="entry-meta meta-0 font-small mb-10">
            <a href="{{ $post->categories->first()->url }}"><span class="post-cat {{ random_color() }}">{{ $post->categories->first()->name }}</span></a>
        </div>
        @endif
        <div class="d-flex post-card-content mt-sm-3">
            <h5 class="post-title mb-20 font-weight-900">
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
