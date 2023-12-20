<?php

namespace Botble\Support\Repositories\Eloquent;

use Botble\Base\Models\BaseModel;
use Botble\Base\Models\BaseQueryBuilder;
use Botble\Base\Supports\RepositoryHelper;
use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

abstract class RepositoriesAbstract implements RepositoryInterface
{
    protected BaseModel|BaseQueryBuilder|Builder|Model $originalModel;

    public function __construct(protected BaseModel|BaseQueryBuilder|Builder|Model $model)
    {
        $this->originalModel = $model;
    }

    public function getModel(): BaseModel|BaseQueryBuilder|Builder|Model
    {
        return new $this->originalModel();
    }

    public function setModel(BaseModel|BaseQueryBuilder|Builder|Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getTable(): string
    {
        if ($this->model instanceof BaseQueryBuilder) {
            return $this->model->getModel()->getTable();
        }

        return $this->model->getTable();
    }

    public function findById($id, array $with = [])
    {
        $data = $this->make($with)->where('id', $id);

        return $this->applyBeforeExecuteQuery($data, true)->first();
    }

    public function make(array $with = [])
    {
        if (! empty($with)) {
            $this->model = $this->model->with($with);
        }

        return $this->model;
    }

    public function applyBeforeExecuteQuery($data, bool $isSingle = false)
    {
        $data = RepositoryHelper::applyBeforeExecuteQuery($data, $this->originalModel, $isSingle);

        $this->resetModel();

        return $data;
    }

    public function resetModel(): self
    {
        $this->model = new $this->originalModel();

        return $this;
    }

    public function findOrFail($id, array $with = [])
    {
        $data = $this->make($with)->where('id', $id);

        $result = $this->applyBeforeExecuteQuery($data, true)->first();

        if (! empty($result)) {
            return $result;
        }

        $model = $this->getModel();

        if ($model instanceof BaseQueryBuilder) {
            $model = $model->getModel();
        }

        throw (new ModelNotFoundException())->setModel(get_class($model), $id);
    }

    public function all(array $with = [])
    {
        $data = $this->make($with);

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function pluck(string $column, $key = null, array $condition = []): array
    {
        $this->applyConditions($condition);

        $select = [$column];
        if (! empty($key)) {
            $select = [$column, $key];
        }

        $data = $this->model->select($select);

        return $this->applyBeforeExecuteQuery($data)->pluck($column, $key)->all();
    }

    public function allBy(array $condition, array $with = [], array $select = ['*'])
    {
        $this->applyConditions($condition);

        $data = $this->make($with)->select($select);

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    protected function applyConditions(array $where, &$model = null)
    {
        if (! $model) {
            $newModel = $this->model;
        } else {
            $newModel = $model;
        }

        foreach ($where as $field => $value) {
            if ($value instanceof Closure) {
                $newModel = $value($newModel);

                continue;
            }

            if (is_array($value)) {
                [$field, $condition, $val] = $value;

                $newModel = match (strtoupper($condition)) {
                    'IN' => $newModel->whereIn($field, $val),
                    'NOT_IN' => $newModel->whereNotIn($field, $val),
                    default => $newModel->where($field, $condition, $val),
                };
            } else {
                $newModel = $newModel->where($field, $value);
            }
        }

        if (! $model) {
            $this->model = $newModel;
        } else {
            $model = $newModel;
        }
    }

    public function create(array $data)
    {
        $data = $this->model->create($data);

        $this->resetModel();

        return $data;
    }

    public function createOrUpdate($data, array $condition = [])
    {
        if (is_array($data)) {
            if (empty($condition)) {
                $item = new $this->originalModel();
            } else {
                $item = $this->getFirstBy($condition);
            }

            if (empty($item)) {
                $item = new $this->originalModel();
            }

            $item = $item->fill($data);
        } elseif ($data instanceof Model) {
            $item = $data;
        } else {
            return false;
        }

        $this->resetModel();

        if ($item->save()) {
            return $item;
        }

        return false;
    }

    public function getFirstBy(array $condition = [], array $select = ['*'], array $with = [])
    {
        $this->resetModel();

        $this->make($with);

        $this->applyConditions($condition);

        if (! empty($select)) {
            $data = $this->model->select($select);
        } else {
            $data = $this->model->select('*');
        }

        return $this->applyBeforeExecuteQuery($data, true)->first();
    }

    public function delete(Model $model): bool|null
    {
        return $model->delete();
    }

    public function firstOrCreate(array $data, array $with = [])
    {
        $data = $this->model->firstOrCreate($data, $with);

        $this->resetModel();

        return $data;
    }

    public function update(array $condition, array $data): int
    {
        $this->applyConditions($condition);

        $data = $this->model->update($data);

        $this->resetModel();

        return $data;
    }

    public function select(array $select = ['*'], array $condition = [])
    {
        $this->applyConditions($condition);

        $data = $this->model->select($select);

        return $this->applyBeforeExecuteQuery($data);
    }

    public function deleteBy(array $condition = []): bool
    {
        $this->applyConditions($condition);

        $data = $this->model->get();

        if ($data->isEmpty()) {
            return false;
        }

        foreach ($data as $item) {
            $item->delete();
        }

        $this->resetModel();

        return true;
    }

    public function count(array $condition = []): int
    {
        $this->applyConditions($condition);

        $data = $this->model->count();

        $this->resetModel();

        return $data;
    }

    public function getByWhereIn($column, array $value = [], array $args = [])
    {
        $data = $this->model->whereIn($column, $value);

        if (! empty(Arr::get($args, 'where'))) {
            $this->applyConditions($args['where']);
        }

        $data = $this->applyBeforeExecuteQuery($data);

        if (! empty(Arr::get($args, 'paginate'))) {
            return $data->paginate((int)$args['paginate']);
        } elseif (! empty(Arr::get($args, 'limit'))) {
            return $data->limit((int)$args['limit']);
        }

        return $data->get();
    }

    public function advancedGet(array $params = [])
    {
        $params = array_merge([
            'condition' => [],
            'order_by' => [],
            'take' => null,
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
            'select' => ['*'],
            'with' => [],
            'withCount' => [],
            'withAvg' => [],
        ], $params);

        $this->applyConditions($params['condition']);

        $data = $this->model;

        if ($params['select']) {
            $data = $data->select($params['select']);
        }

        foreach ($params['order_by'] as $column => $direction) {
            if (! in_array(strtolower($direction), ['asc', 'desc'])) {
                continue;
            }

            if ($direction !== null) {
                $data = $data->orderBy($column, $direction);
            }
        }

        if (! empty($params['with'])) {
            $data = $data->with($params['with']);
        }

        if (! empty($params['withCount'])) {
            $data = $data->withCount($params['withCount']);
        }

        if (! empty($params['withAvg'])) {
            $data = $data->withAvg($params['withAvg'][0], $params['withAvg'][1]);
        }

        if ($params['take'] == 1) {
            $result = $this->applyBeforeExecuteQuery($data, true)->first();
        } elseif ($params['take'] && $params['take'] > 0) {
            $result = $this->applyBeforeExecuteQuery($data)->take((int)$params['take'])->get();
        } elseif ($params['paginate']['per_page']) {
            $paginateType = 'paginate';

            if (Arr::get($params, 'paginate.type') && method_exists($data, Arr::get($params, 'paginate.type'))) {
                $paginateType = Arr::get($params, 'paginate.type');
            }

            $originalModel = $this->originalModel instanceof BaseQueryBuilder ? $this->originalModel->getModel() : $this->originalModel;

            $perPage = (int)Arr::get($params, 'paginate.per_page') ?: 10;

            $currentPage = (int)Arr::get($params, 'paginate.current_paged', 1) ?: 1;

            $result = $this->applyBeforeExecuteQuery($data)
                ->$paginateType(
                    $perPage > 0 ? $perPage : 10,
                    [$originalModel->getTable() . '.' . $originalModel->getKeyName()],
                    'page',
                    $currentPage > 0 ? $currentPage : 1
                );
        } else {
            $result = $this->applyBeforeExecuteQuery($data)->get();
        }

        return $result;
    }

    public function forceDelete(array $condition = [])
    {
        $this->applyConditions($condition);

        $item = $this->model->withTrashed()->first();
        if (! empty($item)) {
            $item->forceDelete();
        }
    }

    public function restoreBy(array $condition = [])
    {
        $this->applyConditions($condition);

        $item = $this->model->withTrashed()->first();
        if (! empty($item)) {
            $item->restore();
        }
    }

    public function getFirstByWithTrash(array $condition = [], array $select = [])
    {
        $this->applyConditions($condition);

        $query = $this->model->withTrashed();

        if (! empty($select)) {
            return $query->select($select)->first();
        }

        return $this->applyBeforeExecuteQuery($query, true)->first();
    }

    public function insert(array $data): bool
    {
        return $this->model->insert($data);
    }

    public function firstOrNew(array $condition)
    {
        $this->applyConditions($condition);

        $result = $this->model->first() ?: new $this->originalModel();

        $this->resetModel();

        return $result;
    }
}
