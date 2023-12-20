<?php

use Botble\Base\Models\BaseModel;
use Botble\CustomField\Facades\CustomField;

if (! function_exists('get_field')) {
    /**
     * @deprecated since v5.17
     */
    function get_field(BaseModel $data, string $key = null, string|array $default = null): string|array|null
    {
        return CustomField::getField($data, $key, $default);
    }
}

if (! function_exists('has_field')) {
    /**
     * @deprecated since v5.17
     */
    function has_field(BaseModel $data, $key = null): bool
    {
        return ! empty(CustomField::getField($data, $key));
    }
}

if (! function_exists('get_sub_field')) {
    function get_sub_field(array $parentField, string $key, string|array $default = null): string|array|null
    {
        return CustomField::getChildField($parentField, $key, $default);
    }
}

if (! function_exists('has_sub_field')) {
    /**
     * @deprecated since v5.17
     */
    function has_sub_field(array $parentField, string $key): bool
    {
        return ! empty(CustomField::getChildField($parentField, $key));
    }
}
