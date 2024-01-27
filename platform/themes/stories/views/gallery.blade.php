@php Theme::set('title', $gallery->name); @endphp
<div class="pb-50">
    <p>{{ $gallery->description }}</p>
    <div id="list-photo">
        @foreach (gallery_meta_data($gallery) as $image)
            @if ($image)
                <div class="item" data-src="{{ RvMedia::getImageUrl(Arr::get($image, 'img')) }}" data-sub-html="{{ BaseHelper::clean(Arr::get($image, 'description')) }}">
                    <div class="photo-item">
                        <div class="thumb">
                            <a href="{{ RvMedia::getImageUrl(Arr::get($image, 'img')) }}">
                                <img src="{{ RvMedia::getImageUrl(Arr::get($image, 'img')) }}" alt="{{ BaseHelper::clean(Arr::get($image, 'description')) }}" loading="lazy">
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <br>
    {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, theme_option('facebook_comment_enabled_in_gallery', 'yes') == 'yes' ? Theme::partial('comments') : null) !!}
</div>
