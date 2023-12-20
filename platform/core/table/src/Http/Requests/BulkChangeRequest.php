<?php

namespace Botble\Table\Http\Requests;

use Botble\Support\Http\Requests\Request;

class BulkChangeRequest extends Request
{
    public function rules(): array
    {
        return [
            'class' => 'required|string',
        ];
    }
}
