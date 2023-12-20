@php
    Assets::addScriptsDirectly(config('core.base.general.editor.tinymce.js'))
        ->addScriptsDirectly('vendor/core/core/base/js/editor.js');

    $attributes['class'] = Arr::get($attributes, 'class', '') . ' form-control editor-tinymce ays-ignore';
    $attributes['id'] = Arr::get($attributes, 'id', $name);
    $attributes['rows'] = Arr::get($attributes, 'rows', 4);
@endphp

{!! Form::textarea($name, BaseHelper::cleanEditorContent($value), $attributes) !!}
