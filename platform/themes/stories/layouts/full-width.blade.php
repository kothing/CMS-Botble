{!! Theme::partial('header') !!}

@if (Theme::get('pageId') && !BaseHelper::isHomepage(Theme::get('pageId')))
    <div class="archive-header pt-50">
        <h2 class="font-weight-900">{{ Theme::get('title') ?: SeoHelper::getTitle() }}</h2>
        {!! Theme::partial('breadcrumbs') !!}
        <div class="bt-1 border-color-1 mt-30 mb-50"></div>
    </div>
@else
    <div class="mt-30 mb-50"></div>
@endif

{!! Theme::content() !!}

{!! Theme::partial('footer') !!}
