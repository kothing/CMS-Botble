<?php

namespace Botble\Translation\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @method ofTranslatedGroup(string|null $group)
 * @method orderByGroupKeys(bool $ordered)
 * @method selectDistinctGroup()
 */
class Translation extends BaseModel
{
    public const STATUS_SAVED = 0;
    public const STATUS_CHANGED = 1;

    protected $table = 'translations';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function scopeOfTranslatedGroup(Builder $query, string|null $group): void
    {
        $query->where('group', $group)->whereNotNull('value');
    }

    public function scopeOrderByGroupKeys(Builder $query, bool $ordered): void
    {
        if ($ordered) {
            $query->orderBy('group')->orderBy('key');
        }
    }

    public function scopeSelectDistinctGroup(Builder $query): void
    {
        $select = match (DB::getDefaultConnection()) {
            'mysql' => 'DISTINCT `group`',
            default => 'DISTINCT "group"',
        };

        $query->select(DB::raw($select));
    }
}
