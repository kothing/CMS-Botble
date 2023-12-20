<?php

namespace Botble\Page\Tables;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Page\Models\Page;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\DataTables;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PageTable extends TableAbstract
{
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, Page $page)
    {
        parent::__construct($table, $urlGenerator);

        $this->model = $page;

        $this->hasActions = true;
        $this->hasFilter = true;

        if (! Auth::user()->hasAnyPermission(['pages.edit', 'pages.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $pageTemplates = get_page_templates();

        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (Page $item) {
                if (! Auth::user()->hasPermission('posts.edit')) {
                    $name = BaseHelper::clean($item->name);
                } else {
                    $name = Html::link(route('pages.edit', $item->getKey()), BaseHelper::clean($item->name));
                }

                if (function_exists('theme_option') && BaseHelper::isHomepage($item->getKey())) {
                    $name .= Html::tag('span', ' — ' . trans('packages/page::pages.front_page'), [
                        'class' => 'additional-page-name',
                    ])->toHtml();
                }

                return apply_filters(PAGE_FILTER_PAGE_NAME_IN_ADMIN_LIST, $name, $item);
            })
            ->editColumn('checkbox', function (Page $item) {
                return $this->getCheckbox($item->getKey());
            })
            ->editColumn('template', function (Page $item) use ($pageTemplates) {
                return Arr::get($pageTemplates, $item->template ?: 'default');
            })
            ->editColumn('created_at', function (Page $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function (Page $item) {
                return $item->status->toHtml();
            })
            ->addColumn('operations', function (Page $item) {
                return $this->getOperations('pages.edit', 'pages.destroy', $item);
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
                'name',
                'template',
                'created_at',
                'status',
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
            'name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'template' => [
                'title' => trans('core/base::tables.template'),
                'class' => 'text-start',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-center',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
                'class' => 'text-center',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('pages.create'), 'pages.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('pages.deletes'), 'pages.destroy', parent::bulkActions());
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
            'template' => [
                'title' => trans('core/base::tables.template'),
                'type' => 'customSelect',
                'choices' => get_page_templates(),
                'validate' => 'required',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
