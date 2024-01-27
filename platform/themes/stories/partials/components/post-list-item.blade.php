<div class="row mb-40 list-style-2">
    <div class="col-md-4">
        <div class="post-thumb position-relative border-radius-5">
            <div class="img-hover-slide border-radius-5 position-relative" style="background-image: url({{ RvMedia::getImageUrl($post->image, null, false, RvMedia::getDefaultImage())}})">
                <a class="img-link" href="{{ $post->url }}"></a>
            </div>
            {!! Theme::partial('social-share-buttons', compact('post')) !!}
        </div>
    </div>
    <div class="col-md-8 align-self-center">
        <div class="post-content">
            @if ($post->categories->first())
                <div class="entry-meta meta-0 font-small mb-10">
                    <a href="{{ $post->categories->first()->url }}"><span class="post-cat {{ random_color() }}">{{ $post->categories->first()->name }}</span></a>
                </div>
            @endif
            <h5 class="post-title font-weight-900 mb-20">
                <a href="{{ $post->url }}">{{ $post->name }}</a>
                <span class="post-format-icon"><i class="elegant-icon icon_star_alt"></i></span>
            </h5>
            <div class="entry-meta meta-1 float-left font-x-small text-uppercase">
                <span class="post-on">{{ $post->created_at->translatedFormat('M d, Y') }}</span>
                <span class="time-reading has-dot">{{ get_time_to_read($post) }} {{ __('mins read') }}</span>
                <span class="post-by has-dot">{{ number_format($post->views) }} {{ __('views') }}</span>
            </div>
        </div>
    </div>
</div>
