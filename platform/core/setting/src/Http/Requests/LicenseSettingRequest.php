<?php

namespace Botble\Setting\Http\Requests;

use Botble\Support\Http\Requests\Request;

class LicenseSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'purchase_code' => 'required|string|min:19|max:36|regex:/^[\pL\s\ \_\-0-9]+$/u',
            'buyer' => 'required|string|max:40|regex:/^[\pL\s\ \_\-0-9]+$/u',
            'license_rules_agreement' => 'accepted:1',
        ];
    }
}
