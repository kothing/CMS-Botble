{!! Theme::partial('header') !!}
@if (Theme::get('section-name'))
    {!! Theme::partial('breadcrumbs') !!}
@endif
{!! Theme::content() !!}
{!! Theme::partial('footer') !!}


