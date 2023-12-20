<?php

namespace Botble\Table\Abstracts;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Form;
use Botble\Base\Facades\Html;
use Botble\Base\Models\BaseModel;
use Botble\Media\Facades\RvMedia;
use Botble\Table\Supports\Builder as CustomTableBuilder;
use Botble\Table\Supports\TableExportHandler;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Services\DataTable;

abstract class TableAbstract extends DataTable
{
    public const TABLE_TYPE_ADVANCED = 'advanced';

    public const TABLE_TYPE_SIMPLE = 'simple';

    protected bool $bStateSave = true;

    protected string $type = self::TABLE_TYPE_ADVANCED;

    protected string $ajaxUrl;

    protected int $pageLength = 10;

    protected $view = 'core/table::table';

    protected string $filterTemplate = 'core/table::filter';

    protected array $options = [];

    protected $hasCheckbox = true;

    protected $hasOperations = true;

    protected $hasActions = false;

    protected string $bulkChangeUrl = '';

    protected $hasFilter = false;

    protected $repository;

    protected BaseModel|null $model = null;

    protected bool $useDefaultSorting = true;

    protected int $defaultSortColumn = 1;

    protected bool $hasResponsive = true;

    protected string $exportClass = TableExportHandler::class;

    public function __construct(protected DataTables $table, UrlGenerator $urlGenerator)
    {
        parent::__construct();

        $this->ajaxUrl = $urlGenerator->current();

        if ($this->type == self::TABLE_TYPE_SIMPLE) {
            $this->pageLength = -1;
        }

        if (! $this->getOption('id')) {
            $this->setOption('id', strtolower(Str::slug(Str::snake(get_class($this)))));
        }

        if (! $this->getOption('class')) {
            $this->setOption('class', 'table table-striped table-hover vertical-middle');
        }

        $this->bulkChangeUrl = route('tables.bulk-change.save');
    }

    public function getOption(string $key): string|null
    {
        return Arr::get($this->options, $key);
    }

    public function setOption(string $key, $value): self
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function isHasFilter(): bool
    {
        return $this->hasFilter;
    }

    public function setHasFilter(bool $hasFilter): self
    {
        $this->hasFilter = $hasFilter;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function html()
    {
        if ($this->request->has('filter_table_id')) {
            $this->bStateSave = false;
        }

        return $this->builder()
            ->columns($this->getColumns())
            ->ajax(['url' => $this->getAjaxUrl(), 'method' => 'POST'])
            ->parameters([
                'dom' => $this->getDom(),
                'buttons' => $this->getBuilderParameters(),
                'initComplete' => $this->htmlInitComplete(),
                'drawCallback' => $this->htmlDrawCallback(),
                'paging' => true,
                'searching' => true,
                'info' => true,
                'searchDelay' => 350,
                'bStateSave' => $this->bStateSave,
                'lengthMenu' => [
                    array_values(
                        array_unique(array_merge(Arr::sortRecursive([10, 30, 50, 100, 500, $this->pageLength]), [-1]))
                    ),
                    array_values(
                        array_unique(
                            array_merge(
                                Arr::sortRecursive([10, 30, 50, 100, 500, $this->pageLength]),
                                [trans('core/base::tables.all')]
                            )
                        )
                    ),
                ],
                'pageLength' => $this->pageLength,
                'processing' => true,
                'serverSide' => true,
                'bServerSide' => true,
                'bDeferRender' => true,
                'bProcessing' => true,
                'language' => [
                    'aria' => [
                        'sortAscending' => 'orderby asc',
                        'sortDescending' => 'orderby desc',
                        'paginate' => [
                            'next' => trans('pagination.next'),
                            'previous' => trans('pagination.previous'),
                        ],
                    ],
                    'emptyTable' => trans('core/base::tables.no_data'),
                    'info' => view('core/table::table-info')->render(),
                    'infoEmpty' => trans('core/base::tables.no_record'),
                    'lengthMenu' => Html::tag('span', '_MENU_', ['class' => 'dt-length-style'])->toHtml(),
                    'search' => '',
                    'searchPlaceholder' => trans('core/table::table.search'),
                    'zeroRecords' => trans('core/base::tables.no_record'),
                    'processing' => Html::image('vendor/core/core/base/images/loading-spinner-blue.gif'),
                    'paginate' => [
                        'next' => trans('pagination.next'),
                        'previous' => trans('pagination.previous'),
                    ],
                    'infoFiltered' => trans('core/table::table.filtered'),
                ],
                'aaSorting' => $this->useDefaultSorting ? [
                    [
                        ($this->hasCheckbox ? $this->defaultSortColumn : 0),
                        'desc',
                    ],
                ] : [],
                'responsive' => $this->hasResponsive,
                'autoWidth' => false,
            ]);
    }

    public function getColumns(): array
    {
        $columns = $this->columns();

        if ($this->type != self::TABLE_TYPE_SIMPLE) {
            foreach ($columns as $key => &$column) {
                $column['class'] = Arr::get($column, 'class') . ' column-key-' . $key;
            }

            if ($this->hasCheckbox) {
                $columns = array_merge($this->getCheckboxColumnHeading(), $columns);
            }
        }

        $columns = apply_filters(BASE_FILTER_TABLE_HEADINGS, $columns, $this->getModel());

        if ($this->hasOperations && $this->type != self::TABLE_TYPE_SIMPLE) {
            $columns = array_merge($columns, $this->getOperationsHeading());
        }

        return $columns;
    }

    protected function getModel(): BaseModel
    {
        return $this->model ?: ($this->repository ? $this->repository->getModel() : new BaseModel());
    }

    public function columns()
    {
        return [];
    }

    public function getOperationsHeading()
    {
        return [
            'operations' => [
                'title' => trans('core/base::tables.operations'),
                'width' => '134px',
                'class' => 'text-center',
                'orderable' => false,
                'searchable' => false,
                'exportable' => false,
                'printable' => false,
                'responsivePriority' => 99,
            ],
        ];
    }

    protected function getOperations(string|null $edit, string|null $delete, Model $item, string|null $extra = null): string
    {
        return apply_filters(
            'table_operation_buttons',
            view('core/table::partials.actions', compact('edit', 'delete', 'item', 'extra'))->render(),
            $item,
            $edit,
            $delete,
            $extra
        );
    }

    public function getCheckboxColumnHeading(): array
    {
        return [
            'checkbox' => [
                'width' => '10px',
                'class' => 'text-start no-sort',
                'title' => Form::input('checkbox', '', null, [
                    'class' => 'table-check-all',
                    'data-set' => '.dataTable .checkboxes',
                ])->toHtml(),
                'orderable' => false,
                'searchable' => false,
                'exportable' => false,
                'printable' => false,
            ],
        ];
    }

    protected function getCheckbox(int|string $id): string
    {
        return view('core/table::partials.checkbox', compact('id'))->render();
    }

    public function getAjaxUrl(): string
    {
        return $this->ajaxUrl;
    }

    public function setAjaxUrl(string $ajaxUrl): self
    {
        $this->ajaxUrl = $ajaxUrl;

        return $this;
    }

    protected function getDom(): string|null
    {
        $dom = null;

        switch ($this->type) {
            case self::TABLE_TYPE_ADVANCED:
                $dom = "fBrt<'datatables__info_wrap'pli<'clearfix'>>";

                break;
            case self::TABLE_TYPE_SIMPLE:
                $dom = "t<'datatables__info_wrap'<'clearfix'>>";

                break;
        }

        return $dom;
    }

    public function getBuilderParameters(): array
    {
        $params = [
            'stateSave' => true,
        ];

        if ($this->type == self::TABLE_TYPE_SIMPLE) {
            return $params;
        }

        $buttons = array_merge($this->getButtons(), $this->getActionsButton());

        $buttons = array_merge($buttons, $this->getDefaultButtons());
        if (! $buttons) {
            return $params;
        }

        return $params + compact('buttons');
    }

    public function getButtons(): array
    {
        $buttons = apply_filters(BASE_FILTER_TABLE_BUTTONS, $this->buttons(), get_class($this->getModel()));

        if (! $buttons) {
            return [];
        }

        $data = [];

        foreach ($buttons as $key => $button) {
            $buttonClass = 'action-item' . (isset($button['class']) ? ' ' . $button['class'] : ' btn-info');

            if (Arr::get($button, 'extend') == 'collection') {
                $button['className'] = ($button['className'] ?? null) . $buttonClass;

                $data[] = $button;
            } else {
                $data[] = [
                    'className' => $buttonClass,
                    'text' => Html::tag('span', $button['text'], [
                        'data-action' => $key,
                        'data-href' => Arr::get($button, 'link'),
                    ])->toHtml(),
                ];
            }
        }

        return $data;
    }

    public function buttons()
    {
        return [];
    }

    public function getActionsButton(): array
    {
        if (! $this->getActions()) {
            return [];
        }

        return [
            [
                'extend' => 'collection',
                'text' => '<span>' . trans('core/base::forms.actions') . ' <span class="caret"></span></span>',
                'buttons' => $this->getActions(),
            ],
        ];
    }

    public function getActions(): array
    {
        if ($this->type == self::TABLE_TYPE_SIMPLE || ! $this->actions()) {
            return [];
        }

        $actions = [];

        foreach ($this->actions() as $key => $action) {
            $actions[] = [
                'className' => 'action-item',
                'text' => '<span data-action="' . $key . '" data-href="' . $action['link'] . '"> ' . $action['text'] . '</span>',
            ];
        }

        return $actions;
    }

    public function actions(): array
    {
        return [];
    }

    public function getDefaultButtons(): array
    {
        return [
            'reload',
        ];
    }

    public function htmlInitComplete(): string|null
    {
        return 'function () {' . $this->htmlInitCompleteFunction() . '}';
    }

    public function htmlInitCompleteFunction(): string|null
    {
        return '
            if (jQuery().select2) {
                $(document).find(".select-multiple").select2({
                    width: "100%",
                    allowClear: true,
                    placeholder: $(this).data("placeholder")
                });
                $(document).find(".select-search-full").select2({
                    width: "100%"
                });
                $(document).find(".select-full").select2({
                    width: "100%",
                    minimumResultsForSearch: -1
                });
            }
        ';
    }

    public function htmlDrawCallback(): string|null
    {
        if ($this->type == self::TABLE_TYPE_SIMPLE) {
            return null;
        }

        return 'function () {' . $this->htmlDrawCallbackFunction() . '}';
    }

    public function htmlDrawCallbackFunction(): string|null
    {
        return '
            var pagination = $(this).closest(".dataTables_wrapper").find(".dataTables_paginate");
            pagination.toggle(this.api().page.info().pages > 1);

            var data_count = this.api().data().count();

            var length_select = $(this).closest(".dataTables_wrapper").find(".dataTables_length");
            var length_info = $(this).closest(".dataTables_wrapper").find(".dataTables_info");
            length_select.toggle(data_count >= 10);
            length_info.toggle(data_count > 0);

            if (jQuery().select2) {
                $(document).find(".select-multiple").select2({
                    width: "100%",
                    allowClear: true,
                    placeholder: $(this).data("placeholder")
                });
                $(document).find(".select-search-full").select2({
                    width: "100%"
                });
                $(document).find(".select-full").select2({
                    width: "100%",
                    minimumResultsForSearch: -1
                });
            }

            $("[data-bs-toggle=tooltip]").tooltip({
                placement: "top"
            });
        ';
    }

    public function renderTable(array $data = [], array $mergeData = []): View|Factory|Response
    {
        return $this->render($this->view, $data, $mergeData);
    }

    public function render(string $view = null, array $data = [], array $mergeData = [])
    {
        Assets::addScripts(['datatables', 'moment', 'datepicker'])
            ->addStyles(['datatables', 'datepicker'])
            ->addStylesDirectly('vendor/core/core/table/css/table.css')
            ->addScriptsDirectly([
                'vendor/core/core/base/libraries/bootstrap3-typeahead.min.js',
                'vendor/core/core/table/js/table.js',
                'vendor/core/core/table/js/filter.js',
            ]);

        $data['id'] = Arr::get($data, 'id', $this->getOption('id'));
        $data['class'] = Arr::get($data, 'class', $this->getOption('class'));

        $this->setAjaxUrl($this->ajaxUrl . '?' . http_build_query(request()->input()));

        $this->setOptions($data);

        $data['actions'] = $this->hasActions ? $this->bulkActions() : [];

        $data['table'] = $this;

        return parent::render($view, $data, $mergeData);
    }

    public function bulkActions(): array
    {
        $actions = [];

        if ($this->getBulkChanges()) {
            $actions['bulk-change'] = view('core/table::bulk-changes', [
                'bulk_changes' => $this->getBulkChanges(),
                'class' => get_class($this),
                'url' => $this->bulkChangeUrl,
            ])->render();
        }

        return $actions;
    }

    public function getBulkChanges(): array
    {
        return [];
    }

    protected function applyScopes(
        EloquentBuilder|QueryBuilder|EloquentRelation|Collection $query
    ): EloquentBuilder|QueryBuilder|EloquentRelation|Collection {
        $request = request();

        $requestFilters = [];

        if (($request->input('filter_table_id') == $this->getOption('id'))) {
            foreach ($this->getFilterColumns() as $key => $item) {
                $operator = $request->input('filter_operators.' . $key);

                $value = $request->input('filter_values.' . $key);

                if (is_array($operator) || is_array($value) || is_array($item)) {
                    continue;
                }

                $requestFilters[] = [
                    'column' => $item,
                    'operator' => $operator,
                    'value' => $value,
                ];
            }
        }

        foreach ($requestFilters as $requestFilter) {
            if (! empty($requestFilter['column'])) {
                $query = $this->applyFilterCondition(
                    $query,
                    $requestFilter['column'],
                    $requestFilter['operator'],
                    $requestFilter['value']
                );
            }
        }

        return parent::applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query));
    }

    public function getFilterColumns(): array
    {
        $columns = $this->getFilters();
        $columnKeys = array_keys($columns);

        return Arr::where((array) $this->request->input('filter_columns', []), function ($item) use ($columnKeys) {
            return in_array($item, $columnKeys);
        });
    }

    public function applyFilterCondition(EloquentBuilder|QueryBuilder|EloquentRelation $query, string $key, string $operator, string|null $value)
    {
        if (strpos($key, '.') !== -1) {
            $key = Arr::last(explode('.', $key));
        }

        $column = $this->getModel()->getTable() . '.' . $key;

        $key = preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '', $key));

        switch ($key) {
            case 'created_at':
            case 'updated_at':
                if (! $value) {
                    break;
                }

                $validator = Validator::make([$key => $value], [$key => 'date']);

                if (! $validator->fails()) {
                    $value = BaseHelper::formatDate($value);
                    $query = $query->whereDate($column, $operator, $value);
                }

                break;

            default:
                if (! $value) {
                    break;
                }

                if ($operator === 'like') {
                    $query = $query->where($column, $operator, '%' . $value . '%');

                    break;
                }

                if ($operator !== '=') {
                    $value = (float)$value;
                }

                $query = $query->where($column, $operator, $value);
        }

        return $query;
    }

    public function getValueInput(string|null $title, string|null $value, string|null $type, array $data = []): array
    {
        $inputName = 'value';

        if (empty($title)) {
            $inputName = 'filter_values[]';
        }
        $attributes = [
            'class' => 'form-control input-value filter-column-value',
            'placeholder' => trans('core/table::table.value'),
            'autocomplete' => 'off',
        ];

        switch ($type) {
            case 'select':
            case 'customSelect':
                $attributes['class'] = $attributes['class'] . ' select';
                $attributes['placeholder'] = trans('core/table::table.select_option');
                $html = Form::customSelect($inputName, $data, $value, $attributes)->toHtml();

                break;

            case 'select-search':
                $attributes['class'] = $attributes['class'] . ' select-search-full';
                $attributes['placeholder'] = trans('core/table::table.select_option');
                $html = Form::customSelect($inputName, $data, $value, $attributes)->toHtml();

                break;

            case 'select-ajax':
                $attributes = [
                    'class' => $attributes['class'] . ' select-search-ajax',
                    'data-url' => Arr::get($data, 'url'),
                    'data-minimum-input' => Arr::get($data, 'minimum-input', 2),
                    'multiple' => Arr::get($data, 'multiple', false),
                    'data-placeholder' => Arr::get($data, 'placeholder', $attributes['placeholder']),
                ];

                $html = Form::customSelect($inputName, Arr::get($data, 'selected', []), $value, $attributes)->toHtml();

                break;

            case 'number':
                $html = Form::number($inputName, $value, $attributes)->toHtml();

                break;

            case 'date':
                $html = Form::date($inputName, $value, $attributes)->toHtml();

                break;

            case 'datePicker':
                $html = Form::datePicker($inputName, $value, $attributes)->toHtml();

                break;

            default:
                $html = Form::text($inputName, $value, $attributes)->toHtml();

                break;
        }

        return compact('html', 'data');
    }

    public function saveBulkChanges(array $ids, string $inputKey, string|null $inputValue): bool
    {
        if (! in_array($inputKey, array_keys($this->getBulkChanges()))) {
            return false;
        }

        $request = request();

        foreach ($ids as $id) {
            $item = $this->getModel()->query()->findOrFail($id);

            /**
             * @var BaseModel $item
             */
            $item = $this->saveBulkChangeItem($item, $inputKey, $inputValue);

            event(new UpdatedContentEvent($this->getModel(), $request, $item));
        }

        return true;
    }

    public function saveBulkChangeItem(Model $item, string $inputKey, string|null $inputValue)
    {
        $item->{Auth::check() ? 'forceFill' : 'fill'}([$inputKey => $this->prepareBulkChangeValue($inputKey, $inputValue)]);

        $item->save();

        return $item;
    }

    public function prepareBulkChangeValue(string $key, string|null $value): string
    {
        if (strpos($key, '.') !== -1) {
            $key = Arr::last(explode('.', $key));
        }

        switch ($key) {
            case 'created_at':
            case 'updated_at':
                $value = BaseHelper::formatDateTime($value);

                break;
        }

        return (string)$value;
    }

    public function renderFilter(): string
    {
        $tableId = $this->getOption('id');
        $class = get_class($this);
        $columns = $this->getFilters();

        $request = request();
        $requestFilters = [
            '-1' => [
                'column' => '',
                'operator' => '=',
                'value' => '',
            ],
        ];

        $filterColumns = $this->getFilterColumns();

        if ($filterColumns) {
            $requestFilters = [];
            foreach ($filterColumns as $key => $item) {
                $operator = $request->input('filter_operators.' . $key);

                $value = $request->input('filter_values.' . $key);

                if (is_array($operator) || is_array($value) || is_array($item)) {
                    continue;
                }

                $requestFilters[] = [
                    'column' => $item,
                    'operator' => $operator,
                    'value' => $value,
                ];
            }
        }

        return view($this->filterTemplate, compact('columns', 'class', 'tableId', 'requestFilters'))->render();
    }

    public function getFilters(): array
    {
        return $this->getBulkChanges();
    }

    protected function addCreateButton(string $url, string|null $permission = null, array $buttons = []): array
    {
        if (! $permission || Auth::user()->hasPermission($permission)) {
            $queryString = http_build_query(Request::query());

            if ($queryString) {
                $url .= '?' . $queryString;
            }

            $buttons['create'] = [
                'link' => $url,
                'text' => view('core/table::partials.create')->render(),
                'class' => 'btn-primary',
            ];
        }

        return $buttons;
    }

    protected function addDeleteAction(string $url, string|null $permission = null, array $actions = []): array
    {
        if (! $permission || Auth::user()->hasPermission($permission)) {
            $actions['delete-many'] = view('core/table::partials.delete', [
                'href' => $url,
                'data_class' => get_called_class(),
            ]);
        }

        return $actions;
    }

    public function toJson($data, array $escapeColumn = [], bool $mDataSupport = true)
    {
        $data = apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->getModel());

        if (BaseModel::determineIfUsingUuidsForId()) {
            $data = $data->editColumn('id', function ($item) {
                if (! $item instanceof BaseModel && ! is_object($item)) {
                    return $item;
                }

                return Str::limit($item->id, 5);
            });
        }

        return $data
            ->escapeColumns($escapeColumn)
            ->make($mDataSupport);
    }

    protected function displayThumbnail(string|null $image, array $attributes = ['width' => 50]): HtmlString|string
    {
        if ($this->request()->input('action') == 'csv') {
            return RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage());
        }

        if ($this->request()->input('action') == 'excel') {
            return RvMedia::getImageUrl($image, 'thumb', false, RvMedia::getDefaultImage());
        }

        return Html::image(
            RvMedia::getImageUrl($image, 'thumb', false, RvMedia::getDefaultImage()),
            trans('core/base::tables.image'),
            $attributes
        );
    }

    public function htmlBuilder(): CustomTableBuilder
    {
        return app(CustomTableBuilder::class);
    }

    protected function simpleDom(): string
    {
        return "rt<'datatables__info_wrap'pli<'clearfix'>>";
    }

    protected function isEmpty(): bool
    {
        return ! $this->request()->wantsJson() &&
            ! $this->request()->ajax() &&
            ! ($this->request()->input('filter_table_id') === $this->getOption('id')) &&
            ! (method_exists($this, 'query') && $this->query()->exists());
    }
}
