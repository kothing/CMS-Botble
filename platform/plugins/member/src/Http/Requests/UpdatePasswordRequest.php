<?php

namespace Botble\Member\Http\Requests;

use Botble\Support\Http\Requests\Request;

class UpdatePasswordRequest extends Request
{
    public function rules(): array
    {
        return [
            'password' => 'required|min:6|max:60|confirmed',
        ];
    }
}
