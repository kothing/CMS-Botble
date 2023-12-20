@php
    $attributes['class'] = Arr::get($attributes, 'class', '') . str_replace(Arr::get($attributes, 'class'), '', ' form-control');
    $attributes['data-date-format'] = $attributes['data-date-format'] ?? config('core.base.general.date_format.date');
    $attributes['placeholder'] = $attributes['data-date-format'];
    $attributes['data-input'] = '';
    $attributes['readonly'] = $attributes['readonly'] ?? 'readonly';

    if (App::getLocale() != 'en') {
        Assets::addScriptsDirectly('https://npmcdn.com/flatpickr@4.6.13/dist/l10n/index.js');
    }

@endphp
<div class="input-group datepicker">
    {!! Form::text($name, $value, $attributes) !!}
    <a class="input-button" title="toggle" data-toggle><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 17 17"><g></g><path d="M14 2V1h-3v1H6V1H3v1H0v15h17V2h-3zM12 2h1v2h-1V2zM4 2h1v2H4V2zM16 16H1v-8.921h15V16zM1 6.079v-3.079h2v2h3V3h5v2h3V3h2v3.079H1z"></path></svg></a>
    <a class="input-button text-danger" title="clear" data-clear><svg xmlns="http://www.w3.org/2000/svg" class="text-danger" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 17 17"><g></g><path d="M9.207 8.5l6.646 6.646-.707.707L8.5 9.207l-6.646 6.646-.707-.707L7.793 8.5 1.146 1.854l.707-.707L8.5 7.793l6.646-6.646.707.707L9.207 8.5z"></path></svg></a>
</div>
