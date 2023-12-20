<?php

namespace Botble\AuditLog;

use Botble\AuditLog\Events\AuditHandlerEvent;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class AuditLog
{
    public function handleEvent(string $screen, Model $data, string $action, string $type = 'info'): bool
    {
        if (! $data instanceof BaseModel || ! $data->getKey()) {
            return false;
        }

        event(new AuditHandlerEvent($screen, $action, $data->getKey(), $this->getReferenceName($screen, $data), $type));

        return true;
    }

    public function getReferenceName(string $screen, Model $data): string
    {
        $name = '';
        switch ($screen) {
            case USER_MODULE_SCREEN_NAME:
            case AUTH_MODULE_SCREEN_NAME:
                $name = $data->name;

                break;
            default:
                if (isset($data->name)) {
                    $name = $data->name;
                } elseif (isset($data->title)) {
                    $name = $data->title;
                } elseif (isset($data->id)) {
                    $name = 'ID: ' . $data->id;
                }
        }

        return $name;
    }
}
