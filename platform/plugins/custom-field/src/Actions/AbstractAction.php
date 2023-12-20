<?php

namespace Botble\CustomField\Actions;

abstract class AbstractAction
{
    protected function error(string|null $message = null, array $data = null): array
    {
        if (! $message) {
            $message = trans('plugins/custom-field::base.error_occurred');
        }

        return response_with_messages($message, true, 500, $data);
    }

    protected function success(string|null $message = null, array $data = null): array
    {
        if (! $message) {
            $message = trans('plugins/custom-field::base.request_completed');
        }

        return response_with_messages(
            $message,
            false,
            ! $data ? 200 : 201,
            $data
        );
    }
}
