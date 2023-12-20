<?php

namespace Botble\Slug\Http\Requests;

use Botble\Slug\Facades\SlugHelper;
use Botble\Support\Http\Requests\Request;
use Illuminate\Support\Str;

class SlugSettingsRequest extends Request
{
    public function rules(): array
    {
        $rules = [];

        $canEmptyPrefixes = SlugHelper::getCanEmptyPrefixes();

        foreach ($this->except(['_token']) as $settingKey => $settingValue) {
            if (! Str::contains($settingKey, '-model-key')) {
                continue;
            }

            $prefixKey = str_replace('-model-key', '', $settingKey);

            $regex = 'regex:/^[\pL\s\ \_\%\-0-9\/]+$/u';

            if (! in_array($settingValue, $canEmptyPrefixes)) {
                $rules[$prefixKey] = 'required|' . $regex;
            } else {
                $rules[$prefixKey] = 'nullable|' . $regex;
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];
        foreach (SlugHelper::supportedModels() as $model => $name) {
            $attributes[SlugHelper::getPermalinkSettingKey($model)] = trans('packages/slug::slug.prefix_for', ['name' => $name]);
        }

        return $attributes;
    }
}
