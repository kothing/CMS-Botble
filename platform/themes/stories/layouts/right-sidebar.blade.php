{!! Theme::partial('header') !!}

<div class="container">
    @if (Theme::get('pageId') && !BaseHelper::isHomepage(Theme::get('pageId')))
        <div class="archive-header pt-50">
            <h2 class="font-weight-900">{{ Theme::get('title') ?: SeoHelper::getTitle() }}</h2>
            @if (Theme::get('subtitle'))
                <p>{!! Theme::get('subtitle') !!}</p>
            @endif
            {!! Theme::partial('breadcrumbs') !!}
            <div class="bt-1 border-color-1 mt-30 mb-50"></div>
        </div>
    @else
        <div class="mt-30 mb-50"></div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            {!! Theme::content() !!}
        </div>
        <div class="col-lg-4 primary-sidebar sticky-sidebar">
            {!! display_ad('top-sidebar-ads', ['class' => 'mb-30']) !!}
            {!! dynamic_sidebar('primary_sidebar') !!}
            {!! display_ad('bottom-sidebar-ads', ['class' => 'mt-30 mb-30']) !!}
            <br>
            <br>
        </div>
    </div>
</div>

{!! Theme::partial('footer') !!}
