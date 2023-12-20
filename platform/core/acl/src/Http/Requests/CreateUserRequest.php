<?php

namespace Botble\ACL\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CreateUserRequest extends Request
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:60|min:2',
            'last_name' => 'required|max:60|min:2',
            'email' => 'required|max:60|min:6|email|unique:users',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'username' => 'required|alpha_dash|min:4|max:30|unique:users',
        ];
    }
}
