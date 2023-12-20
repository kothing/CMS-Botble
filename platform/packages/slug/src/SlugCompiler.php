<?php

namespace Botble\Slug;

use Botble\Base\Models\BaseModel;
use Carbon\Carbon;

class SlugCompiler
{
    public function getVariables(): array
    {
        $now = Carbon::now();

        return apply_filters('cms_slug_variables', [
            '%%year%%' => [
                'label' => trans('packages/slug::slug.current_year'),
                'value' => $now->year,
            ],
            '%%month%%' => [
                'label' => trans('packages/slug::slug.current_month'),
                'value' => $now->month,
            ],
            '%%day%%' => [
                'label' => trans('packages/slug::slug.current_day'),
                'value' => $now->month,
            ],
        ]);
    }

    public function compile(string|null $prefix, BaseModel|string|null $model = null): string
    {
        if (! $prefix) {
            return '';
        }

        foreach ($this->getVariables() as $key => $value) {
            $prefix = str_replace($key, $value['value'], $prefix);
        }

        return apply_filters('cms_slug_prefix', $prefix, $model);
    }
}
