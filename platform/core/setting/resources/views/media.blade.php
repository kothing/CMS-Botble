@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="max-width-1200">
        {!! Form::open(['route' => ['settings.media']]) !!}
            <x-core-setting::section
                :title="trans('core/setting::setting.media.title')"
                :description="trans('core/setting::setting.media.description')"
            >
                <x-core-setting::select
                    name="media_driver"
                    :label="trans('core/setting::setting.media.driver')"
                    :options="[
                        'public' => 'Local disk',
                        's3' => 'Amazon S3',
                        'r2' => 'Cloudflare R2',
                        'do_spaces' => 'DigitalOcean Spaces',
                        'wasabi' => 'Wasabi',
                        'bunnycdn' => 'BunnyCDN',
                    ]"
                    :value="RvMedia::getMediaDriver()"
                    class="setting-select-options"
                />

                <div data-type="s3" @class(['setting-wrapper', 'hidden' => old('media_driver', RvMedia::getMediaDriver()) !== 's3'])>
                    <x-core-setting::text-input
                        name="media_aws_access_key_id"
                        :label="trans('core/setting::setting.media.aws_access_key_id')"
                        :value="config('filesystems.disks.s3.key')"
                        placeholder="Ex: AKIAIKYXBSNBXXXXXX"
                    />

                    <x-core-setting::text-input
                        name="media_aws_secret_key"
                        :label="trans('core/setting::setting.media.aws_secret_key')"
                        :value="config('filesystems.disks.s3.secret')"
                        placeholder="Ex: +fivlGCeTJCVVnzpM2WfzzrFIMLHGhxxxxxxx"
                    />

                    <x-core-setting::text-input
                        name="media_aws_default_region"
                        :label="trans('core/setting::setting.media.aws_default_region')"
                        :value="config('filesystems.disks.s3.region')"
                        placeholder="Ex: ap-southeast-1"
                    />

                    <x-core-setting::text-input
                        name="media_aws_bucket"
                        :label="trans('core/setting::setting.media.aws_bucket')"
                        :value="config('filesystems.disks.s3.bucket')"
                        placeholder="Ex: botble"
                    />

                    <x-core-setting::text-input
                        name="media_aws_url"
                        :label="trans('core/setting::setting.media.aws_url')"
                        :value="config('filesystems.disks.s3.url')"
                        placeholder="Ex: https://s3-ap-southeast-1.amazonaws.com/botble"
                    />

                    <x-core-setting::text-input
                        name="media_aws_endpoint"
                        :label="trans('core/setting::setting.media.aws_endpoint')"
                        :value="config('filesystems.disks.s3.endpoint')"
                        :placeholder="trans('core/setting::setting.media.optional')"
                    />
                </div>

                <div data-type="r2" @class(['setting-wrapper', 'hidden' => old('media_driver', RvMedia::getMediaDriver()) !== 'r2'])>
                    <x-core-setting::text-input
                        name="media_r2_access_key_id"
                        :label="trans('core/setting::setting.media.r2_access_key_id')"
                        :value="config('filesystems.disks.r2.key')"
                        placeholder="Ex: AKIAIKYXBSNBXXXXXX"
                    />

                    <x-core-setting::text-input
                        name="media_r2_secret_key"
                        :label="trans('core/setting::setting.media.r2_secret_key')"
                        :value="config('filesystems.disks.r2.secret')"
                        placeholder="Ex: +fivlGCeTJCVVnzpM2WfzzrFIMLHGhxxxxxxx"
                    />

                    <x-core-setting::text-input
                        name="media_r2_bucket"
                        :label="trans('core/setting::setting.media.r2_bucket')"
                        :value="config('filesystems.disks.r2.bucket')"
                        placeholder="Ex: botble"
                    />

                    <x-core-setting::text-input
                        name="media_r2_endpoint"
                        :label="trans('core/setting::setting.media.r2_endpoint')"
                        :value="config('filesystems.disks.r2.endpoint')"
                        placeholder="Ex: https://xxx.r2.cloudflarestorage.com"
                    />

                    <x-core-setting::text-input
                        name="media_r2_url"
                        :label="trans('core/setting::setting.media.r2_url')"
                        :value="config('filesystems.disks.r2.url')"
                        placeholder="Ex: https://pub-f70218cc331a40689xxx.r2.dev"
                    />
                </div>

                <div data-type="do_spaces" @class(['setting-wrapper', 'hidden' => old('media_driver', RvMedia::getMediaDriver()) !== 'do_spaces'])>
                    <x-core-setting::text-input
                        name="media_do_spaces_access_key_id"
                        :label="trans('core/setting::setting.media.do_spaces_access_key_id')"
                        :value="config('filesystems.disks.do_spaces.key')"
                        placeholder="Ex: AKIAIKYXBSNBXXXXXX"
                    />

                    <x-core-setting::text-input
                        name="media_do_spaces_secret_key"
                        :label="trans('core/setting::setting.media.do_spaces_secret_key')"
                        :value="config('filesystems.disks.do_spaces.secret')"
                        placeholder="Ex: +fivlGCeTJCVVnzpM2WfzzrFIMLHGhxxxxxxx"
                    />

                    <x-core-setting::text-input
                        name="media_do_spaces_default_region"
                        :label="trans('core/setting::setting.media.do_spaces_default_region')"
                        :value="config('filesystems.disks.do_spaces.region')"
                        placeholder="Ex: SGP1"
                    />

                    <x-core-setting::text-input
                        name="media_do_spaces_bucket"
                        :label="trans('core/setting::setting.media.do_spaces_bucket')"
                        :value="config('filesystems.disks.do_spaces.bucket')"
                        placeholder="Ex: botble"
                    />

                    <x-core-setting::text-input
                        name="media_do_spaces_endpoint"
                        :label="trans('core/setting::setting.media.do_spaces_endpoint')"
                        :value="config('filesystems.disks.do_spaces.endpoint')"
                        placeholder="Ex: https://sfo2.digitaloceanspaces.com"
                    />

                    <x-core-setting::form-group>
                        <input type="hidden" name="media_do_spaces_cdn_enabled" value="0">
                        <label>
                            <input type="checkbox"  value="1" @if (setting('media_do_spaces_cdn_enabled')) checked @endif name="media_do_spaces_cdn_enabled">
                            {{ trans('core/setting::setting.media.do_spaces_cdn_enabled') }}
                        </label>
                    </x-core-setting::form-group>

                    <x-core-setting::text-input
                        name="media_do_spaces_cdn_custom_domain"
                        :label="trans('core/setting::setting.media.media_do_spaces_cdn_custom_domain')"
                        :value="setting('media_do_spaces_cdn_custom_domain')"
                        :placeholder="trans('core/setting::setting.media.media_do_spaces_cdn_custom_domain_placeholder')"
                    />
                </div>

                <div data-type="wasabi" @class(['setting-wrapper', 'hidden' => old('media_driver', RvMedia::getMediaDriver()) !== 'wasabi'])>
                    <x-core-setting::text-input
                        name="media_wasabi_access_key_id"
                        :label="trans('core/setting::setting.media.wasabi_access_key_id')"
                        :value="config('filesystems.disks.wasabi.key')"
                        placeholder="Ex: AKIAIKYXBSNBXXXXXX"
                    />

                    <x-core-setting::text-input
                        name="media_wasabi_secret_key"
                        :label="trans('core/setting::setting.media.wasabi_secret_key')"
                        :value="config('filesystems.disks.wasabi.secret')"
                        placeholder="Ex: +fivlGCeTJCVVnzpM2WfzzrFIMLHGhxxxxxxx"
                    />

                    <x-core-setting::text-input
                        name="media_wasabi_default_region"
                        :label="trans('core/setting::setting.media.wasabi_default_region')"
                        :value="config('filesystems.disks.wasabi.region')"
                        placeholder="Ex: us-east-1"
                    />

                    <x-core-setting::text-input
                        name="media_wasabi_bucket"
                        :label="trans('core/setting::setting.media.wasabi_bucket')"
                        :value="config('filesystems.disks.wasabi.bucket')"
                        placeholder="Ex: botble"
                    />

                    <x-core-setting::text-input
                        name="media_wasabi_root"
                        :label="trans('core/setting::setting.media.wasabi_root')"
                        :value="config('filesystems.disks.wasabi.root')"
                        placeholder="Default: /"
                    />
                </div>

                <div data-type="bunnycdn" @class(['setting-wrapper', 'hidden' => old('media_driver', RvMedia::getMediaDriver()) !== 'bunnycdn'])>
                    <x-core-setting::text-input
                        name="media_bunnycdn_hostname"
                        :label="trans('core/setting::setting.media.bunnycdn_hostname')"
                        :value="setting('media_bunnycdn_hostname')"
                        placeholder="Ex: botble.b-cdn.net"
                    />

                    <x-core-setting::text-input
                        name="media_bunnycdn_zone"
                        :label="trans('core/setting::setting.media.bunnycdn_zone')"
                        :value="setting('media_bunnycdn_zone')"
                        placeholder="Ex: botble"
                    />

                    <x-core-setting::text-input
                        name="media_bunnycdn_key"
                        :label="trans('core/setting::setting.media.bunnycdn_key')"
                        :value="setting('media_bunnycdn_key')"
                        placeholder="Ex: 9a734df7-844b-..."
                    />

                    <x-core-setting::select
                        name="media_bunnycdn_region"
                        :label="trans('core/setting::setting.media.bunnycdn_region')"
                        :options="[
                            '' => 'Falkenstein',
                            'ny' => 'New York',
                            'la' => 'Los Angeles',
                            'sg' => 'Singapore',
                            'syd' => 'Sydney',
                        ]"
                        :value="setting('media_bunnycdn_region')"
                    />
                </div>

                <x-core-setting::on-off
                    name="media_turn_off_automatic_url_translation_into_latin"
                    :label="trans('core/setting::setting.media.turn_off_automatic_url_translation_into_latin')"
                    :value="RvMedia::turnOffAutomaticUrlTranslationIntoLatin()"
                />

                <x-core-setting::form-group>
                    <label class="text-title-field" for="media_default_placeholder_image">{{ trans('core/setting::setting.media.default_placeholder_image') }}</label>
                    {!! Form::mediaImage('media_default_placeholder_image', setting('media_default_placeholder_image')) !!}
                </x-core-setting::form-group>

                <x-core-setting::text-input
                    name="max_upload_filesize"
                    :label="trans('core/setting::setting.media.max_upload_filesize')"
                    type="number"
                    :value="setting('max_upload_filesize')"
                    step="0.01"
                    :placeholder="trans('core/setting::setting.media.max_upload_filesize_placeholder', ['size' => $maxSize = BaseHelper::humanFilesize(RvMedia::getServerConfigMaxUploadFileSize())])"
                    :helper-text="trans('core/setting::setting.media.max_upload_filesize_helper', ['size' => $maxSize])"
                />

                <x-core-setting::on-off
                    name="media_chunk_enabled"
                    :label="trans('core/setting::setting.media.enable_chunk')"
                    :value="RvMedia::isChunkUploadEnabled()"
                    class="setting-selection-option"
                    data-target="#media-chunk-settings"
                    :helper-text="trans('core/setting::setting.enable_chunk_description')"
                />

                <div id="media-chunk-settings" class="mb-4 border rounded-top rounded-bottom p-3 bg-light @if (!RvMedia::isChunkUploadEnabled()) d-none @endif">
                    <div class="row">
                        <div class="col-lg-6">
                            <x-core-setting::text-input
                                name="media_chunk_size"
                                :label="trans('core/setting::setting.media.chunk_size')"
                                type="number"
                                :value="setting('media_chunk_size', RvMedia::getConfig('chunk.chunk_size'))"
                                :placeholder="trans('core/setting::setting.media.chunk_size_placeholder')"
                            />
                        </div>
                        <div class="col-lg-6">
                            <x-core-setting::text-input
                                name="media_max_file_size"
                                :label="trans('core/setting::setting.media.max_file_size')"
                                type="number"
                                :value="setting('media_max_file_size', RvMedia::getConfig('chunk.max_file_size'))"
                                :placeholder="trans('core/setting::setting.media.max_file_size_placeholder')"
                            />
                        </div>
                    </div>
                </div>

                <x-core-setting::on-off
                    name="media_watermark_enabled"
                    :label="trans('core/setting::setting.media.enable_watermark')"
                    :value="setting('media_watermark_enabled', RvMedia::getConfig('watermark.enabled', false))"
                    class="setting-selection-option"
                    data-target="#media-watermark-settings"
                />

                <div id="media-watermark-settings" @class(['mb-4 border rounded-top rounded-bottom p-3 bg-light', 'd-none' => ! setting('media_watermark_enabled', RvMedia::getConfig('watermark.enabled', false))])>
                    <x-core-setting::form-group>
                        {{ Form::helper(trans('core/setting::setting.watermark_description')) }}
                    </x-core-setting::form-group>

                    <x-core-setting::form-group>
                        <label class="text-title-field" for="media_folders_can_add_watermark">{{ trans('core/setting::setting.media.media_folders_can_add_watermark') }}</label>
                        <label>
                            <input type="checkbox" class="check-all" data-set=".media-folder">
                            {{ trans('core/setting::setting.media.all') }}
                        </label>
                        <div class="form-group form-group-no-margin">
                            <div class="multi-choices-widget list-item-checkbox">
                                <ul>
                                    @foreach ($folders as $key => $item)
                                        <li>
                                            <input
                                                type="checkbox"
                                                class="styled media-folder"
                                                name="media_folders_can_add_watermark[]"
                                                value="{{ $key }}"
                                                id="media-folder-item-{{ $key }}"
                                                @checked(empty($folderIds) || in_array($key, $folderIds))
                                            >
                                            <label for="media-folder-item-{{ $key }}">{{ $item }}</label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </x-core-setting::form-group>

                    <x-core-setting::form-group>
                        <label class="text-title-field" for="media_watermark_source">{{ trans('core/setting::setting.media.watermark_source') }}</label>
                        {!! Form::mediaImage('media_watermark_source', setting('media_watermark_source')) !!}
                    </x-core-setting::form-group>

                    <div class="row">
                        <div class="col-lg-6">
                            <x-core-setting::text-input
                                name="media_watermark_size"
                                :label="trans('core/setting::setting.media.watermark_size')"
                                type="number"
                                :value="setting('media_watermark_size', RvMedia::getConfig('watermark.size'))"
                                :placeholder="trans('core/setting::setting.media.watermark_size_placeholder')"
                            />
                        </div>
                        <div class="col-lg-6">
                            <x-core-setting::text-input
                                name="watermark_opacity"
                                :label="trans('core/setting::setting.media.watermark_opacity')"
                                type="number"
                                :value="setting('watermark_opacity', RvMedia::getConfig('watermark.opacity'))"
                                :placeholder="trans('core/setting::setting.media.watermark_opacity_placeholder')"
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <x-core-setting::select
                                name="media_watermark_position"
                                :label="trans('core/setting::setting.media.watermark_position')"
                                :options="[
                                    'top-left' => trans('core/setting::setting.media.watermark_position_top_left'),
                                    'top-right' => trans('core/setting::setting.media.watermark_position_top_right'),
                                    'bottom-left' => trans('core/setting::setting.media.watermark_position_bottom_left'),
                                    'bottom-right' => trans('core/setting::setting.media.watermark_position_bottom_right'),
                                    'center' => trans('core/setting::setting.media.watermark_position_center'),
                                ]"
                                :value="setting('media_watermark_position', RvMedia::getConfig('watermark.position'))"
                            />
                        </div>
                        <div class="col-lg-4">
                            <x-core-setting::text-input
                                name="watermark_position_x"
                                :label="trans('core/setting::setting.media.watermark_position_x')"
                                type="number"
                                :value="setting('watermark_position_x', RvMedia::getConfig('watermark.x'))"
                                :placeholder="trans('core/setting::setting.media.watermark_position_x')"
                            />
                        </div>
                        <div class="col-lg-4">
                            <x-core-setting::text-input
                                name="watermark_position_y"
                                :label="trans('core/setting::setting.media.watermark_position_y')"
                                type="number"
                                :value="setting('watermark_position_y', RvMedia::getConfig('watermark.y'))"
                                :placeholder="trans('core/setting::setting.media.watermark_position_y')"
                            />
                        </div>
                    </div>
                </div>

                <x-core-setting::radio
                    name="media_image_processing_library"
                    :label="trans('core/setting::setting.media.image_processing_library')"
                    :value="RvMedia::getImageProcessingLibrary()"
                    :options="array_merge(['gd' => 'GD Library'], extension_loaded('imagick') ? [
                        'imagick' => 'Imagick',
                    ] : [])"
                />

                <hr>

                <div>
                    <h5 class="mb-3">{{ trans('core/setting::setting.media.sizes') }}:</h5>
                    @foreach(RvMedia::getSizes() as $name => $size)
                        @php($sizeExploded = explode('x', $size))

                        @if (count($sizeExploded))
                            <x-core-setting::form-group>
                                <label class="text-title-field">{{ str_replace('-', ' ', Str::title(Str::slug($name))) }} <small>({{ trans('core/setting::setting.media.default_size_value', ['size' => RvMedia::getConfig('sizes.' . $name)]) }})</small></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="next-input--stylized">
                                            <span class="next-input-add-on next-input__add-on--before">{{ trans('core/setting::setting.media.width') }}:</span>
                                            <input type="number" class="next-input next-input--invisible" name="media_sizes_{{ $name }}_width" value="{{ setting('media_sizes_' . $name . '_width', $sizeExploded[0]) }}" placeholder="0">
                                            <span class="next-input-add-on next-input__add-on--after">px</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="next-input--stylized">
                                            <span class="next-input-add-on next-input__add-on--before">{{ trans('core/setting::setting.media.height') }}:</span>
                                            <input type="number" class="next-input next-input--invisible" name="media_sizes_{{ $name }}_height" value="{{ setting('media_sizes_' . $name . '_height', $sizeExploded[1]) }}" placeholder="0">
                                            <span class="next-input-add-on next-input__add-on--after">px</span>
                                        </div>
                                    </div>
                                </div>
                            </x-core-setting::form-group>
                        @endif
                    @endforeach
                    {{ Form::helper(trans('core/setting::setting.media.media_sizes_helper')) }}
                </div>
            </x-core-setting::section>

            <div class="flexbox-annotated-section" style="border: none">
                <div class="flexbox-annotated-section-annotation">&nbsp;</div>
                <div class="flexbox-annotated-section-content">
                    <button class="btn btn-info" type="submit">{{ trans('core/setting::setting.save_settings') }}</button> &nbsp;
                    <button class="btn btn-warning generate-thumbnails-trigger-button" type="button" data-saving="{{ trans('core/setting::setting.saving') }}">{{ trans('core/setting::setting.generate_thumbnails') }}</button>
                </div>
            </div>
        {!! Form::close() !!}
    </div>

    <x-core-base::modal
        id="generate-thumbnails-modal"
        :title="trans('core/setting::setting.generate_thumbnails')"
        type="warning"
        button-id="generate-thumbnails-button"
        :button-label="trans('core/setting::setting.generate')"
    >
        {!! trans('core/setting::setting.generate_thumbnails_description') !!}
    </x-core-base::modal>

    {!! $jsValidation !!}
@endsection
