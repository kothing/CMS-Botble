<?php

namespace Botble\Base\Supports\Database;

use Botble\Base\Models\BaseModel;
use Closure;
use Illuminate\Database\Schema\Blueprint as IlluminateBlueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Support\Facades\DB;
use Throwable;

class Blueprint extends IlluminateBlueprint
{
    public function __construct($table, Closure $callback = null, $prefix = '')
    {
        parent::__construct($table, $callback, $prefix);

        rescue(fn () => DB::statement('SET SESSION sql_require_primary_key=0'), report: false);
    }

    public function id($column = 'id'): ColumnDefinition
    {
        return match ($this->getModelTypeOfId()) {
            'UUID' => $this->uuid($column)->primary(),
            'ULID' => $this->ulid($column)->primary(),
            default => parent::id($column),
        };
    }

    public function foreignId($column): ColumnDefinition
    {
        return match ($this->getModelTypeOfId()) {
            'UUID' => $this->foreignUuid($column),
            'ULID' => $this->foreignUlid($column),
            default => parent::foreignId($column),
        };
    }

    public function morphs($name, $indexName = null): void
    {
        match ($this->getModelTypeOfId()) {
            'UUID' => $this->uuidMorphs($name, $indexName),
            'ULID' => $this->ulidMorphs($name, $indexName),
            default => parent::morphs($name, $indexName),
        };
    }

    public function nullableMorphs($name, $indexName = null): void
    {
        match ($this->getModelTypeOfId()) {
            'UUID' => $this->nullableUuidMorphs($name, $indexName),
            'ULID' => $this->nullableUlidMorphs($name, $indexName),
            default => parent::nullableMorphs($name, $indexName),
        };
    }

    protected function getModelTypeOfId(): string
    {
        try {
            return BaseModel::getTypeOfId();
        } catch (Throwable) {
            return 'BIGINT';
        }
    }
}
