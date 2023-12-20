<nav aria-label="breadcrumb" class="d-inline-block">
    <ol class="breadcrumb">
        @foreach ($crumbs = Theme::breadcrumb()->getCrumbs() as $i => $crumb)
            @if ($i != (count($crumbs) - 1))
                <li class="breadcrumb-item">
                    <a href="{{ $crumb['url'] }}" title="{{ $crumb['label'] }}">
                        {{ $crumb['label'] }}
                    </a>
                </li>
            @else
                <li class="breadcrumb-item active">
                    <span>
                        {{ $crumb['label'] }}
                    </span>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
