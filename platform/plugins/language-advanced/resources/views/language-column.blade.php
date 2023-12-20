<div class="text-center language-column">
    @foreach($languages as $language)
        @if (! is_in_admin() || (Auth::check() && Auth::user()->hasPermission($route['edit'])))
                @if ($language->lang_code == Language::getDefaultLocaleCode())
                    <a href="{{ Route::has($route['edit']) ? route($route['edit'], $item->id) : '#' }}">
                        <i class="fa fa-check text-success"></i>
                    </a>
                @else
                    <a href="{{ Route::has($route['edit']) ? route($route['edit'], $item->id) . '?ref_lang=' . $language->lang_code : '#' }}"
                       data-bs-toggle="tooltip"
                       title="{{ trans('plugins/language::language.edit_related') }}"
                    >
                        <i class="fa fa-edit"></i>
                    </a>
                @endif
        @else
            <i class="fa fa-check text-success"></i>
        @endif
    @endforeach
</div>
