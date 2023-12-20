<?php

namespace Botble\RequestLog\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Models\BaseQueryBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Query\Builder;

class RequestLog extends BaseModel
{
    use MassPrunable;

    protected $table = 'request_logs';

    protected $fillable = [
        'url',
        'status_code',
    ];

    protected $casts = [
        'referrer' => 'json',
        'user_id' => 'json',
    ];

    public function prunable(): Builder|BaseQueryBuilder
    {
        return static::where('created_at', '<=', Carbon::now()->subMonth());
    }
}
