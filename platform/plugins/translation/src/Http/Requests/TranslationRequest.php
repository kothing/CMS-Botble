<?php

namespace Botble\Translation\Http\Requests;

use Botble\Support\Http\Requests\Request;

class TranslationRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:220',
        ];
    }
}
