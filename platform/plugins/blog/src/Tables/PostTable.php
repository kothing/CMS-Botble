<?php

namespace Botble\Blog\Tables;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Blog\Exports\PostExport;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\DataTables;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PostTable extends TableAbstract
{
    protected string $exportClass = PostExport::class;

    protected int $defaultSortColumn = 6;

    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        Post $post
    ) {
        parent::__construct($table, $urlGenerator);

        $this->model = $post;

        $this->hasActions = true;
        $this->hasFilter = true;

        if (! Auth::user()->hasAnyPermission(['posts.edit', 'posts.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (Post $item) {
                if (! Auth::user()->hasPermission('posts.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('posts.edit', $item->getKey()), BaseHelper::clean($item->name));
            })
            ->editColumn('image', function (Post $item) {
                return $this->displayThumbnail($item->image);
            })
            ->editColumn('checkbox', function (Post $item) {
                return $this->getCheckbox($item->getKey());
            })
            ->editColumn('created_at', function (Post $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('updated_at', function (Post $item) {
                $categories = '';
                foreach ($item->categories as $category) {
                    $categories .= Html::link(route('categories.edit', $category->id), $category->name) . ', ';
                }

                return rtrim($categories, ', ');
            })
            ->editColumn('author_id', function (Post $item) {
                return $item->author && $item->author->name ? BaseHelper::clean($item->author->name) : '&mdash;';
            })
            ->editColumn('status', function (Post $item) {
                if ($this->request()->input('action') === 'excel') {
                    return $item->status->getValue();
                }

                return BaseHelper::clean($item->status->toHtml());
            })
            ->addColumn('operations', function (Post $item) {
                return $this->getOperations('posts.edit', 'posts.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->with([
                'categories' => function ($query) {
                    $query->select(['categories.id', 'categories.name']);
                },
                'author',
            ])
            ->select([
                'id',
                'name',
                'image',
                'created_at',
                'status',
                'updated_at',
                'author_id',
                'author_type',
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
            'image' => [
                'title' => trans('core/base::tables.image'),
                'width' => '70px',
            ],
            'name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'updated_at' => [
                'title' => trans('plugins/blog::posts.categories'),
                'width' => '150px',
                'class' => 'no-sort text-center',
                'orderable' => false,
            ],
            'author_id' => [
                'title' => trans('plugins/blog::posts.author'),
                'width' => '150px',
                'class' => 'no-sort text-center',
                'orderable' => false,
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
        return $this->addCreateButton(route('posts.create'), 'posts.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('posts.deletes'), 'posts.destroy', parent::bulkActions());
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
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'category' => [
                'title' => trans('plugins/blog::posts.category'),
                'type' => 'select-search',
                'validate' => 'required|string',
                'callback' => 'getCategories',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
                'validate' => 'required|string|date',
            ],
        ];
    }

    public function getCategories(): array
    {
        return Category::query()->pluck('name', 'id')->all();
    }

    public function applyFilterCondition(EloquentBuilder|QueryBuilder|EloquentRelation $query, string $key, string $operator, string|null $value): EloquentRelation|EloquentBuilder|QueryBuilder
    {
        if ($key === 'category' && $value) {
            return $query->whereHas('categories', fn ($query) => $query->where('categories.id', $value));
        }

        return parent::applyFilterCondition($query, $key, $operator, $value);
    }

    public function saveBulkChangeItem(Model|Post $item, string $inputKey, string|null $inputValue): Model|bool
    {
        if ($inputKey === 'category') {
            /**
             * @var Post $item
             */
            $item->categories()->sync([$inputValue]);

            return $item;
        }

        return parent::saveBulkChangeItem($item, $inputKey, $inputValue);
    }

    public function getDefaultButtons(): array
    {
        return [
            'export',
            'reload',
        ];
    }
}
