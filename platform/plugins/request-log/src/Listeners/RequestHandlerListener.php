<?php

namespace Botble\RequestLog\Listeners;

use Botble\RequestLog\Events\RequestHandlerEvent;
use Botble\RequestLog\Models\RequestLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RequestHandlerListener
{
    public function __construct(protected Request $request)
    {
    }

    public function handle(RequestHandlerEvent $event): bool
    {
        try {
            $url = $this->request->fullUrl();

            if (Str::contains($url, '.js.map')) {
                return false;
            }

            if (! Cache::has('pruned_request_logs_table')) {
                (new RequestLog())->pruneAll();

                Cache::put('pruned_request_logs_table', 1, Carbon::now()->addDay());
            }

            $requestLog = RequestLog::query()->firstOrNew([
                'url' => Str::limit($url, 120),
                'status_code' => $event->code,
            ]);

            if ($referrer = $this->request->header('referrer')) {
                $requestLog->referrer = array_filter(array_unique(array_merge((array)$requestLog->referrer, [$referrer])));
            }

            if (Auth::check()) {
                $requestLog->user_id = array_filter(array_unique(array_merge((array)$requestLog->user_id, [Auth::id()])));
            }

            $requestLog->count = $requestLog->exists ? $requestLog->count + 1 : 1;

            return $requestLog->save();
        } catch (Exception $exception) {
            info($exception->getMessage());

            return false;
        }
    }
}
