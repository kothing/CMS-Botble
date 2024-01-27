<div class="sidebar-widget widget-about mb-50 pt-30 pr-30 pb-30 pl-30 bg-white border-radius-5 has-border  wow fadeInUp animated">
    @if ($config['image'])
        <img class="about-author-img mb-25" src="{{ RvMedia::getImageUrl($config['image']) }}" alt="{{ $config['name'] }}">
    @endif
    <h5 class="mb-20">{{ $config['name'] }}</h5>
    <p class="font-medium text-muted">{{ $config['description'] }}</p>
    <strong>{{ __('Follow me') }}: </strong>
    <ul class="header-social-network d-inline-block list-inline color-white mb-20">
        @for ($i = 1; $i <= 5; $i++)
            @if (theme_option('social_' . $i . '_url') && theme_option('social_' . $i . '_name'))
                <li class="list-inline-item"><a style="background: {{ theme_option('social_' . $i . '_color') }}" href="{{ theme_option('social_' . $i . '_url') }}" target="_blank" title="{{ theme_option('social_' . $i . '_name') }}"><i class="elegant-icon {{ theme_option('social_' . $i . '_icon') }}"></i></a></li>
            @endif
        @endfor
    </ul>
</div>
