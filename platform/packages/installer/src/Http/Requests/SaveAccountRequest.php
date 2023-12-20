<?php

namespace Botble\Installer\Http\Requests;

use Botble\ACL\Http\Requests\CreateUserRequest;

class SaveAccountRequest extends CreateUserRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['username'] = 'required|alpha_dash|min:4|max:30';

        return $rules;
    }
}
