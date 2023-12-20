@php
    $prefix = apply_filters(FILTER_SLUG_PREFIX, $prefix);
    $value = $value ?: old('slug');
    $endingURL = SlugHelper::getPublicSingleEndingURL();
    $previewURL = str_replace('--slug--', (string) $value, url($prefix) . '/' . config('packages.slug.general.pattern')) . $endingURL . (Auth::user() && $preview ? '?preview=true' : '');
@endphp

<div id="edit-slug-box" @if (empty($value) && !$errors->has($name)) class="hidden" @endif data-field-name="{{ SlugHelper::getColumnNameToGenerateSlug($model) }}">
    @if (in_array(Route::currentRouteName(), ['pages.create', 'pages.edit']) && BaseHelper::isHomepage(Route::current()->parameter('page.id')))
        <label class="control-label me-1" for="current-slug">{{ trans('core/base::forms.permalink') }}</label>
        <span id="sample-permalink" class="d-inline-block" dir="ltr">
            : <a class="permalink" target="_blank" href="{{ route('public.index') }}">
                <span class="default-slug">{{ route('public.index') }}</span>
            </a>
        </span>
    @else
        <label class="control-label me-1 @if ($editable) required @endif" for="current-slug">{{ trans('core/base::forms.permalink') }}</label>
        <span id="sample-permalink" class="d-inline-block" dir="ltr">
            : <a class="permalink" target="_blank" href="{{ $previewURL }}">
                <span class="default-slug">{{ url($prefix) }}/<span id="editable-post-name">{{ $value }}</span>{{ $endingURL }}</span>
            </a>
        </span>

        @if ($editable)
            <span id="edit-slug-buttons">
                <button type="button" class="btn btn-secondary ms-1" id="change_slug">{{ trans('core/base::forms.edit') }}</button>
                <button type="button" class="save btn btn-secondary ms-1" id="btn-ok">{{ trans('core/base::forms.ok') }}</button>
                <button type="button" class="cancel button-link ms-1">{{ trans('core/base::forms.cancel') }}</button>
                @if (Auth::user() && $preview && $id)
                    <a class="btn btn-info btn-preview" target="_blank" href="{{ $previewURL }}">{{ trans('packages/slug::slug.preview') }}</a>
                @endif
            </span>

            <input type="hidden" id="current-slug" name="{{ $name }}" value="{{ $value }}">
            <div data-url="{{ route('slug.create') }}" data-view="{{ rtrim(str_replace('--slug--', '', url($prefix) . '/' . config('packages.slug.general.pattern')), '/') . '/' }}" id="slug_id" data-id="{{ $id ?: 0 }}"></div>
            <input type="hidden" name="slug_id" value="{{ $id ?: 0 }}">
            <input type="hidden" name="is_slug_editable" value="1">
        @endif
    @endif
</div>
