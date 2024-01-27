@php
    Theme::set('pageId', $page->id);
    Theme::set('title', $page->name);
@endphp

{!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, BaseHelper::clean($page->content), $page) !!}
