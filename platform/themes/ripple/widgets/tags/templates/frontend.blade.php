@if ($sidebar == 'footer_sidebar')
    <div class="col-lg-3 col-md-3 col-sm-6 col-12">
        <div class="widget widget--transparent widget__footer widget__tags widget__tags--transparent">
            @else
                <div class="widget widget__tags widget--transparent widget__tags--transparent">
                    @endif
                    <div class="widget__header">
                        <h3 class="widget__title">{{ $config['name'] }}</h3>
                    </div>
                    <div class="widget__content">
                        <p>
                            @foreach ($tags as $tag)
                                <a href="{{ $tag->url }}" class="tag-link">{{ $tag->name }}</a>
                            @endforeach
                        </p>
                    </div>
                </div>
                @if ($sidebar == 'footer_sidebar')
        </div>
@endif
