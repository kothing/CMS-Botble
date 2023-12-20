<?php

namespace Botble\Translation\Http\Requests;

use Botble\Base\Supports\Language;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class LocaleRequest extends Request
{
    public function rules(): array
    {
        return [
            'locale' => ['required', Rule::in(collect(Language::getListLanguages())->pluck(0)->unique()->all())],
        ];
    }
}
