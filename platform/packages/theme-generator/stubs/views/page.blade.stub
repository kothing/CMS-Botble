<h3>{{ $page->name }}</h3>
{!! Theme::breadcrumb()->render() !!}

{!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, Html::tag('div', BaseHelper::clean($page->content), ['class' => 'ck-content'])->toHtml(), $page) !!}
