<?php

namespace Botble\Member\Http\Requests;

use Botble\Blog\Http\Requests\PostRequest as BasePostRequest;
use Botble\Media\Facades\RvMedia;

class PostRequest extends BasePostRequest
{
    public function rules(): array
    {
        $imageRule = str_replace('required|', '', RvMedia::imageValidationRule());

        return parent::rules() + ['image_input' => $imageRule];
    }
}
