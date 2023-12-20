<?php

namespace Botble\AuditLog\Facades;

use Botble\AuditLog\AuditLog as BaseAuditLog;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool handleEvent(string $screen, \Illuminate\Database\Eloquent\Model $data, string $action, string $type = 'info')
 * @method static string getReferenceName(string $screen, \Illuminate\Database\Eloquent\Model $data)
 *
 * @see \Botble\AuditLog\AuditLog
 */
class AuditLog extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaseAuditLog::class;
    }
}
