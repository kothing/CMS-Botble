<?php

namespace Botble\AuditLog\Listeners;

use Botble\AuditLog\Events\AuditHandlerEvent;
use Botble\AuditLog\Models\AuditHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuditHandlerListener
{
    public function __construct(protected Request $request)
    {
    }

    public function handle(AuditHandlerEvent $event): void
    {
        try {
            $data = [
                'user_agent' => $this->request->userAgent(),
                'ip_address' => $this->request->ip(),
                'module' => $event->module,
                'action' => $event->action,
                'user_id' => $this->request->user() ? $this->request->user()->getKey() : 0,
                'reference_user' => $event->referenceUser,
                'reference_id' => $event->referenceId,
                'reference_name' => $event->referenceName,
                'type' => $event->type,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            if (! in_array($event->action, ['loggedin', 'password'])) {
                $data['request'] = json_encode($this->request->except([
                    'username',
                    'password',
                    're_password',
                    'new_password',
                    'current_password',
                    'password_confirmation',
                    '_token',
                    'token',
                    'refresh_token',
                    'remember_token',
                ]));
            }

            if (! Cache::has('pruned_audit_logs_table')) {
                (new AuditHistory())->pruneAll();

                Cache::put('pruned_audit_logs_table', 1, Carbon::now()->addDay());
            }

            AuditHistory::query()->insert($data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
