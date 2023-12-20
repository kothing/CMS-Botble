<?php

use Botble\CustomField\Facades\CustomField;
use Botble\CustomField\Support\CustomFieldSupport;

if (! function_exists('add_custom_fields_rules_to_check')) {
    function add_custom_fields_rules_to_check(string|array $ruleName, $value = null): CustomFieldSupport
    {
        return CustomField::addRule($ruleName, $value);
    }
}

if (! function_exists('get_custom_field_boxes')) {
    function get_custom_field_boxes(string|object $morphClass, int|string|null $morphId): array
    {
        if (is_object($morphClass)) {
            $morphClass = get_class($morphClass);
        }

        return CustomField::exportCustomFieldsData($morphClass, $morphId);
    }
}

if (! function_exists('response_with_messages')) {
    function response_with_messages(string|array $messages, bool $error = false, int $responseCode = null, array|string|null $data = null): array
    {
        return [
            'error' => $error,
            'response_code' => $responseCode ?: 200,
            'messages' => (array)$messages,
            'data' => $data,
        ];
    }
}
