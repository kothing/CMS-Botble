<?php

namespace Botble\Setting\Http\Requests;

use Botble\Media\Facades\RvMedia;
use Botble\Support\Http\Requests\Request;

class MediaSettingRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'media_driver' => 'required|string|in:public,s3,r2,do_spaces,wasabi,bunnycdn',
            'media_aws_access_key_id' => 'nullable|string|required_if:media_driver,s3',
            'media_aws_secret_key' => 'nullable|string|required_if:media_driver,s3',
            'media_aws_default_region' => 'nullable|string|required_if:media_driver,s3',
            'media_aws_bucket' => 'nullable|string|required_if:media_driver,s3',
            'media_aws_url' => 'nullable|string|required_if:media_driver,s3',

            'media_r2_access_key_id' => 'nullable|string|required_if:media_driver,r2',
            'media_r2_secret_key' => 'nullable|string|required_if:media_driver,r2',
            'media_r2_bucket' => 'nullable|string|required_if:media_driver,r2',
            'media_r2_endpoint' => 'nullable|string|required_if:media_driver,r2',
            'media_r2_url' => 'nullable|string|required_if:media_driver,r2',

            'media_wasabi_access_key_id' => 'nullable|string|required_if:media_driver,wasabi',
            'media_wasabi_secret_key' => 'nullable|string|required_if:media_driver,wasabi',
            'media_wasabi_default_region' => 'nullable|string|required_if:media_driver,wasabi',
            'media_wasabi_bucket' => 'nullable|string|required_if:media_driver,wasabi',
            'media_wasabi_root' => 'nullable|string',

            'media_do_spaces_access_key_id' => 'nullable|string|required_if:media_driver,do_spaces',
            'media_do_spaces_secret_key' => 'nullable|string|required_if:media_driver,do_spaces',
            'media_do_spaces_default_region' => 'nullable|string|required_if:media_driver,do_spaces',
            'media_do_spaces_bucket' => 'nullable|string|required_if:media_driver,do_spaces',
            'media_do_spaces_endpoint' => 'nullable|string|required_if:media_driver,do_spaces',

            'media_bunnycdn_hostname' => 'nullable|string|required_if:media_driver,bunnycdn',
            'media_bunnycdn_zone' => 'nullable|string|required_if:media_driver,bunnycdn',
            'media_bunnycdn_key' => 'nullable|string|required_if:media_driver,bunnycdn',
            'media_bunnycdn_region' => 'nullable|string|max:200|required_if:media_driver,bunnycdn',

            'media_watermark_enabled' => 'nullable|in:0,1',
            'media_image_processing_library' => 'nullable|in:gd,imagick',
        ];

        foreach (array_keys(RvMedia::getSizes()) as $size) {
            $rules['media_sizes_' . $size . '_width'] = 'required|numeric|min:0';
            $rules['media_sizes_' . $size . '_height'] = 'required|numeric|min:0';
        }

        return apply_filters('cms_media_settings_validation_rules', $rules);
    }

    public function attributes(): array
    {
        $attributes = [];

        foreach (array_keys(RvMedia::getSizes()) as $size) {
            $attributes['media_sizes_' . $size . '_width'] = trans('core/setting::setting.media_size_width', ['size' => ucfirst($size)]);
            $attributes['media_sizes_' . $size . '_height'] = trans('core/setting::setting.media_size_height', ['size' => ucfirst($size)]);
        }

        return $attributes;
    }
}
