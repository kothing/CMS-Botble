@if (!Arr::get($attributes, 'without-buttons', false))
    <div class="d-flex mb-2">
        @php $result = Arr::get($attributes, 'id', $name); @endphp
        <div class="d-inline-block editor-action-item action-show-hide-editor">
            <button class="btn btn-primary show-hide-editor-btn" type="button" data-result="{{ $result }}">{{ trans('core/base::forms.show_hide_editor') }}</button>
        </div>
        <div class="d-inline-block editor-action-item">
            <a href="#" class="btn_gallery btn btn-primary"
               data-result="{{ $result }}"
               data-multiple="true"
               data-action="media-insert-{{ BaseHelper::getRichEditor() }}">
                <i class="far fa-image"></i> {{ trans('core/media::media.add') }}
            </a>
        </div>
        @if (function_exists('shortcode') && Arr::get($attributes, 'with-short-code', false))
            <div class="d-inline-block editor-action-item list-shortcode-items">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle add_shortcode_btn_trigger" data-result="{{ $result }}" type="button" data-bs-toggle="dropdown"><i class="fa fa-code"></i> {{ trans('core/base::forms.short_code') }}
                    </button>
                    <ul class="dropdown-menu">
                        @foreach (shortcode()->getAll() as $key => $item)
                            @continue(!isset($item['name']))
                            @if ($item['name'])
                                <li>
                                    <a href="{{ route('short-codes.ajax-get-admin-config', $key) }}" data-has-admin-config="{{ Arr::has($item, 'admin_config') }}"
                                       data-key="{{ $key }}" data-description="{{ $item['description'] }}" data-preview-image="{{ Arr::get($item, 'previewImage') }}"
                                    >{{ $item['name'] }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            @once
                @push('footer')
                    <div class="modal fade short_code_modal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h4 class="modal-title"><i class="til_img"></i><strong>{{ trans('core/base::forms.add_short_code') }}</strong></h4>
                                    <div class="float-end">
                                        <a class="shortcode-preview-image-link bold color-white" style="color: #fff" target="_blank" href="">{{ trans('core/base::forms.view_preview_image') }}</a>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                    </div>
                                </div>

                                <div class="modal-body with-padding">
                                    <form class="form-horizontal short-code-data-form">
                                        <input type="hidden" class="short_code_input_key">

                                        @include('core/base::elements.loading')

                                        <div class="short-code-admin-config"></div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="float-start btn btn-secondary" data-bs-dismiss="modal">{{ trans('core/base::tables.cancel') }}</button>
                                    <button type="button" class="float-end btn btn-primary add_short_code_btn" data-add-text="{{ trans('core/base::forms.add') }}" data-update-text="{{ trans('core/base::forms.update') }}">{{ trans('core/base::forms.add') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endpush
            @endonce
        @endif

        {!! apply_filters(BASE_FILTER_FORM_EDITOR_BUTTONS, null) !!}
    </div>
    <div class="clearfix"></div>
@else
    @php Arr::forget($attributes, 'with-short-code'); @endphp
@endif

{!! call_user_func_array([Form::class, BaseHelper::getRichEditor()], [$name, $value, $attributes]) !!}
