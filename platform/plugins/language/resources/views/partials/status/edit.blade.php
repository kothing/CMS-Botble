@if (!is_in_admin() || (Auth::check() && Auth::user()->hasPermission($route['edit'])))
    <a href="{{ Route::has($route['edit']) ? route($route['edit'], $relatedLanguage) : '#' }}" data-bs-toggle="tooltip" data-bs-original-title="{{ trans('plugins/language::language.edit_related') }}"><i class="fa fa-edit"></i></a>
@else
    <i class="fa fa-check text-success"></i>
@endif
