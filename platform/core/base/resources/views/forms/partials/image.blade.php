@php
    $allowThumb = Arr::get($attributes, 'allow_thumb', true);
    $defaultImage = Arr::get($attributes, 'default_image', RvMedia::getDefaultImage());
@endphp
<div class="image-box">
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" class="image-data">
    @if (! is_in_admin(true) || ! auth()->check())
        <input type="file" @if($name) name="{{ $name }}_input" @endif class="media-image-input" @if (! isset($attributes['action']) || $attributes['action'] == 'select-image') accept="image/*" @endif style="display: none;">
    @endif
    <div class="preview-image-wrapper @if (!$allowThumb) preview-image-wrapper-not-allow-thumb @endif">
        <img src="{{ $image ?? RvMedia::getImageUrl($value, $allowThumb ? 'thumb' : null, false, $defaultImage) }}"
            data-default="{{ $defaultImage }}"
            alt="{{ trans('core/base::base.preview_image') }}"
            class="preview_image" @if ($allowThumb) width="150" @endif>
        <a class="btn_remove_image" title="{{ trans('core/base::forms.remove_image') }}">
            <i class="fa fa-times"></i>
        </a>
    </div>
    <div class="image-box-actions">
        <a href="#" class="@if (is_in_admin(true) && auth()->check()) btn_gallery @else media-select-image @endif" data-result="{{ $name }}"
            data-action="{{ $attributes['action'] ?? 'select-image' }}" data-allow-thumb="{{ $allowThumb == true }}">
            {{ trans('core/base::forms.choose_image') }}
        </a>
    </div>
</div>
