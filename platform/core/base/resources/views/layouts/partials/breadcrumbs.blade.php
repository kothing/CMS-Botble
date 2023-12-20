@if (count($breadcrumbs))
    <ol class="breadcrumb" v-pre>
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($breadcrumb->url && !$loop->last)
                <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
            @else
                <li class="breadcrumb-item active">{{ Str::limit($breadcrumb->title, 60) }}</li>
            @endif
        @endforeach
    </ol>
@endif
