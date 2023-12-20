<?php

namespace Botble\Table\Http\Requests;

use Botble\Support\Http\Requests\Request;

class FilterRequest extends Request
{
    public function rules(): array
    {
        return [
            'class' => 'required|string',
        ];
    }
}
