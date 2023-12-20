<?php

namespace Botble\AuditLog\Tables;

use Botble\AuditLog\Models\AuditHistory;
use Botble\Base\Facades\Html;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\DataTables;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuditLogTable extends TableAbstract
{
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AuditHistory $auditHistory)
    {
        parent::__construct($table, $urlGenerator);

        $this->model = $auditHistory;

        $this->hasActions = true;
        $this->hasFilter = false;

        if (! Auth::user()->hasPermission('audit-log.destroy')) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function (AuditHistory $item) {
                return $this->getCheckbox($item->getKey());
            })
            ->editColumn('action', function (AuditHistory $item) {
                return view('plugins/audit-log::activity-line', ['history' => $item])->render();
            })
            ->addColumn('operations', function (AuditHistory $item) {
                return $this->getOperations(null, 'audit-log.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->with(['user'])
            ->select(['*']);

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
            'action' => [
                'name' => 'action',
                'title' => trans('plugins/audit-log::history.action'),
                'class' => 'text-start',
            ],
            'user_agent' => [
                'name' => 'user_agent',
                'title' => trans('plugins/audit-log::history.user_agent'),
                'class' => 'text-start',
            ],
        ];
    }

    public function buttons(): array
    {
        return [
            'empty' => [
                'link' => route('audit-log.empty'),
                'text' => Html::tag('i', '', ['class' => 'fa fa-trash'])->toHtml() . ' ' . trans('plugins/audit-log::history.delete_all'),
            ],
        ];
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('audit-log.deletes'), 'audit-log.destroy', parent::bulkActions());
    }
}
