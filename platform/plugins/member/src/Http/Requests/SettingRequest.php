<?php

namespace Botble\Member\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:120',
            'last_name' => 'required|max:120',
            'phone' => 'max:20|sometimes',
            'dob' => 'max:20|sometimes',
        ];
    }
}
