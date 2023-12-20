{!! Form::hidden('gallery', $value ? json_encode($value) : null, ['id' => 'gallery-data', 'class' => 'form-control']) !!}
<div>
    <div class="list-photos-gallery">
        <div class="row" id="list-photos-items">
            @if (!empty($value))
                @foreach ($value as $key => $item)
                    <div class="col-md-2 col-sm-3 col-4 photo-gallery-item" data-id="{{ $key }}" data-img="{{ Arr::get($item, 'img') }}" data-description="{{ Arr::get($item, 'description') }}">
                        <div class="gallery_image_wrapper">
                            <img src="{{ RvMedia::getImageUrl(Arr::get($item, 'img'), 'thumb') }}" alt="{{ trans('plugins/gallery::gallery.item') }}">
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="form-group mb-3">
        <a href="#" class="btn_select_gallery">{{ trans('plugins/gallery::gallery.select_image') }}</a>&nbsp;
        <a href="#" class="text-danger reset-gallery @if (empty($value)) hidden @endif">{{ trans('plugins/gallery::gallery.reset') }}</a>
    </div>
</div>

<x-core-base::modal
    id="edit-gallery-item"
    :title="trans('plugins/gallery::gallery.update_photo_description')"
    type="danger"
    button-id="confirm-remove-plugin-button"
    :button-label="trans('packages/plugin-management::plugin.remove_plugin_confirm_yes')"
>
    <p><input type="text" class="form-control" id="gallery-item-description" placeholder="{{ trans('plugins/gallery::gallery.update_photo_description_placeholder') }}"></p>
    <x-slot name="footer">
        <button class="float-start btn btn-danger" type="button" id="delete-gallery-item">{{ trans('plugins/gallery::gallery.delete_photo') }}</button>
        <button class="float-end btn btn-secondary" type="button" data-bs-dismiss="modal">{{ trans('core/base::forms.cancel') }}</button>
        <button class="float-end btn btn-primary" type="button" id="update-gallery-item">{{ trans('core/base::forms.update') }}</button>
    </x-slot>
</x-core-base::modal>

