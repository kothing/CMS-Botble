<?php

namespace Botble\Setting\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SendTestEmailRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }
}
