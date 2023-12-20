<?php

namespace Botble\RequestLog\Tables;

use Botble\Base\Facades\Html;
use Botble\RequestLog\Models\RequestLog;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\DataTables;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RequestLogTable extends TableAbstract
{
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        RequestLog $requestLog
    ) {
        parent::__construct($table, $urlGenerator);

        $this->model = $requestLog;

        $this->hasActions = true;

        if (! Auth::user()->hasPermission('request-log.destroy')) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function (RequestLog $item) {
                return $this->getCheckbox($item->getKey());
            })
            ->editColumn('url', function (RequestLog $item) {
                return Html::link($item->url, $item->url, ['target' => '_blank'])->toHtml();
            })
            ->addColumn('operations', function (RequestLog $item) {
                return $this->getOperations(null, 'request-log.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'id',
                'url',
                'status_code',
                'count',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'url' => [
                'title' => trans('core/base::tables.url'),
                'class' => 'text-start',
            ],
            'status_code' => [
                'title' => trans('plugins/request-log::request-log.status_code'),
            ],
            'count' => [
                'title' => trans('plugins/request-log::request-log.count'),
            ],
        ];
    }

    public function buttons(): array
    {
        return [
            'empty' => [
                'link' => route('request-log.empty'),
                'text' => Html::tag('i', '', ['class' => 'fa fa-trash'])->toHtml() .
                    ' ' . trans('plugins/request-log::request-log.delete_all'),
            ],
        ];
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('request-log.deletes'), 'request-log.destroy', parent::bulkActions());
    }
}
