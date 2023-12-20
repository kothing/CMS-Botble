<?php

namespace Botble\Language\Http\Requests;

use Botble\Base\Supports\Language;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class LanguageRequest extends Request
{
    public function rules(): array
    {
        $languages = collect(Language::getListLanguages());

        return [
            'lang_name' => 'required|string|max:30|min:2',
            'lang_code' => [
                'required',
                'string',
                Rule::in($languages->pluck('1')->unique()->all()),
            ],
            'lang_locale' => [
                'required',
                'string',
                Rule::in($languages->pluck('0')->unique()->all()),
            ],
            'lang_flag' => 'required|string',
            'lang_is_rtl' => 'required|string',
            'lang_order' => 'required|numeric',
        ];
    }
}
