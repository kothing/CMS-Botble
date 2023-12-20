@if ($posts->isNotEmpty())
    @foreach ($posts as $post)
        <article>
            <div>
                <a href="{{ $post->url }}"><img src="{{ RvMedia::getImageUrl($post->image, null, false, RvMedia::getDefaultImage()) }}" alt="{{ $post->name }}"></a>
            </div>
            <div>
                <header>
                    <h3><a href="{{ $post->url }}">{{ $post->name }}</a></h3>
                    <div><span>{{ $post->created_at->format('M d, Y') }}</span><span>{{ $post->author->name }}</span> -
                        {{ __('Categories') }}:
                        @foreach($post->categories as $category)
                            <a href="{{ $category->url }}">{{ $category->name }}</a>
                            @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </div>
                </header>
                <div>
                    <p>{{ $post->description }}</p>
                </div>
            </div>
        </article>
    @endforeach
    <div>
        {!! $posts->withQueryString()->links() !!}
    </div>
@endif
