<div class="image-box attachment-wrapper">
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" class="attachment-url">
    @if (! is_in_admin(true) || ! auth()->check())
        <input type="file" @if($name) name="{{ $name }}_input" @endif class="media-file-input" style="display: none;">
    @endif
    <div class="attachment-details">
        <a href="{{ $url ?? $value }}" target="_blank">{{ $value }}</a>
    </div>
    <div class="image-box-actions">
        <a href="#" class="@if (is_in_admin(true) && auth()->check()) btn_gallery @else media-select-file @endif" data-result="{{ $name }}" data-action="{{ $attributes['action'] ?? 'attachment' }}">
            {{ trans('core/base::forms.choose_file') }}
        </a> |
        <a href="#" class="text-danger btn_remove_attachment">
            {{ trans('core/base::forms.remove_file') }}
        </a>
    </div>
</div>
