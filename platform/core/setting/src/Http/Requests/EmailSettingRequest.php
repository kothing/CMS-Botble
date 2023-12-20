<?php

namespace Botble\Setting\Http\Requests;

use Botble\Support\Http\Requests\Request;

class EmailSettingRequest extends Request
{
    public function rules(): array
    {
        return apply_filters('cms_email_settings_validation_rules', [
            'email_driver' => 'required|in:smtp,mailgun,ses,postmark,log,array,sendmail',
            'email_from_name' => 'required|string|max:150',
            'email_from_address' => 'required|email|min:6|max:150',
        ]);
    }
}
