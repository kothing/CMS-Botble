<div class="breadcrumb">
    @foreach (Theme::breadcrumb()->getCrumbs() as $i => $crumb)
        @if ($i != (count(Theme::breadcrumb()->getCrumbs()) - 1))
            <a href="{{ $crumb['url'] }}">{!! $crumb['label'] !!}</a><span></span>
        @else
            {!! $crumb['label'] !!}
        @endif
    @endforeach
</div>
