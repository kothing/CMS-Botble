@if ($paginator->lastPage() > 1)
    <{{ $wrapperTag }} class="{{ $wrapperClass }}">
        <{{ $groupTag }} class="{{ $groupClass }}">
            <{{ $itemTag }} class="{{ $itemClass }} {{ ($paginator->currentPage() == 1) ? ' ' . $disabledClass : '' }} go-first">
                <a href="{{ $paginator->url(1) }}" class="arrow">{!! $goFirstTitle !!}</a>
            </{{ $itemTag }}>
            <{{ $itemTag }} class="{{ $itemClass }} {{ (!$paginator->previousPageUrl()) ? ' ' . $disabledClass : '' }} previous">
                <a href="{{ $paginator->previousPageUrl() }}" class="arrow">{!! $goPrevTitle !!}</a>
            </{{ $itemTag }}>
            @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                @php
                    $half_total_links = floor($limit / 2);
                    $from = $paginator->currentPage() - $half_total_links;
                    $to = $paginator->currentPage() + $half_total_links;
                    if ($paginator->currentPage() < $half_total_links) {
                        $to += $half_total_links - $paginator->currentPage();
                    }
                    if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
                        $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
                    }
                @endphp
                @if ($from < $i && $i < $to)
                    <{{ $itemTag }} class="{{ $itemClass }} {{ ($paginator->currentPage() == $i) ? ' ' . $activatedClass : '' }}">
                        <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
                    </{{ $itemTag }}>
                @endif
            @endfor
            <{{ $itemTag }} class="{{ $itemClass }} {{ (!$paginator->nextPageUrl()) ? ' ' . $disabledClass : '' }} next">
                <a href="{{ $paginator->nextPageUrl() }}" class="arrow">{!! $goNextTitle !!}</a>
            </{{ $itemTag }}>
            <{{ $itemTag }} class="{{ $itemClass }} {{ ($paginator->currentPage() == $paginator->lastPage()) ? ' ' . $disabledClass : '' }} go-last">
                <a href="{{ $paginator->url($paginator->lastPage()) }}" class="arrow">{!! $goLastTitle !!}</a>
            </{{ $itemTag }}>
        </{{ $groupTag }}>
        <span class="pagination-text">Page {{ $paginator->currentPage() . '/' . $paginator->lastPage() }}</span>
    </{{ $wrapperTag }}>
@endif
