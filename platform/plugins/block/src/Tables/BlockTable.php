<?php

namespace Botble\Block\Tables;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Block\Models\Block;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\DataTables;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BlockTable extends TableAbstract
{
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, Block $block)
    {
        parent::__construct($table, $urlGenerator);

        $this->model = $block;

        $this->hasActions = true;
        $this->hasFilter = true;

        if (! Auth::user()->hasAnyPermission(['block.edit', 'block.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (Block $item) {
                if (! Auth::user()->hasPermission('block.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('block.edit', $item->getKey()), BaseHelper::clean($item->name));
            })
            ->editColumn('checkbox', function (Block $item) {
                return $this->getCheckbox($item->getKey());
            })
            ->editColumn('created_at', function (Block $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function (Block $item) {
                return $item->status->toHtml();
            })
            ->addColumn('operations', function (Block $item) {
                return $this->getOperations('block.edit', 'block.destroy', $item);
            });

        if (function_exists('shortcode')) {
            $data = $data->editColumn('alias', function (Block $item) {
                return generate_shortcode('static-block', ['alias' => $item->alias]);
            });
        }

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'id',
                'alias',
                'name',
                'created_at',
                'status',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name' => 'name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'alias' => [
                'name' => 'alias',
                'title' => trans('core/base::tables.shortcode'),
                'class' => 'text-start',
            ],
            'created_at' => [
                'name' => 'created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name' => 'status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('block.create'), 'block.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('block.deletes'), 'block.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'customSelect',
                'choices' => BaseStatusEnum::labels(),
                'validate' => 'required|' . Rule::in(BaseStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
