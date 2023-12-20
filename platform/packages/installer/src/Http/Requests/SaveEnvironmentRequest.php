<?php

namespace Botble\Installer\Http\Requests;

use Botble\Installer\Enums\DatabaseConnectionsEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class SaveEnvironmentRequest extends Request
{
    public function rules(): array
    {
        return [
            'app_name' => 'required|string|max:120',
            'app_url' => 'required|url',
            'database_connection' => 'required|string|max:60|' . Rule::in(DatabaseConnectionsEnum::values()),
            'database_hostname' => 'required|string|max:255',
            'database_port' => 'required|numeric',
            'database_name' => 'required|string|max:60',
            'database_username' => 'required|string|max:60',
            'database_password' => 'nullable|string|max:60',
        ];
    }

    public function messages(): array
    {
        return [
            'environment_custom.required_if' => trans(
                'packages/installer::installer.environment.wizard.form.name_required'
            ),
        ];
    }
}
