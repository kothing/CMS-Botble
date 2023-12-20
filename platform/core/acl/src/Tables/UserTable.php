<?php

namespace Botble\ACL\Tables;

use Botble\ACL\Enums\UserStatusEnum;
use Botble\ACL\Models\User;
use Botble\ACL\Services\ActivateUserService;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Exceptions\DisabledInDemoModeException;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\DataTables;
use Exception;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class UserTable extends TableAbstract
{
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        User $user,
        protected ActivateUserService $service
    ) {
        parent::__construct($table, $urlGenerator);

        $this->model = $user;

        $this->hasActions = true;
        $this->hasFilter = true;

        if (! Auth::user()->hasAnyPermission(['users.edit', 'users.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function (User $item) {
                return $this->getCheckbox($item->getKey());
            })
            ->editColumn('username', function (User $item) {
                if (! Auth::user()->hasPermission('users.edit')) {
                    return $item->username;
                }

                return Html::link(route('users.profile.view', $item->getKey()), $item->username);
            })
            ->editColumn('created_at', function (User $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('role_name', function (User $item) {
                if (! Auth::user()->hasPermission('users.edit')) {
                    return $item->role_name;
                }

                return view('core/acl::users.partials.role', ['item' => $item])->render();
            })
            ->editColumn('super_user', function (User $item) {
                return $item->super_user ? trans('core/base::base.yes') : trans('core/base::base.no');
            })
            ->editColumn('status', function (User $item) {
                if ($item->activations()->where('completed', true)->exists()) {
                    return UserStatusEnum::ACTIVATED()->toHtml();
                }

                return UserStatusEnum::DEACTIVATED()->toHtml();
            })
            ->removeColumn('role_id')
            ->addColumn('operations', function (User $item) {
                $action = null;
                if (Auth::user()->isSuperUser()) {
                    $action = Html::link(
                        route('users.make-super', $item->getKey()),
                        trans('core/acl::users.make_super'),
                        ['class' => 'btn btn-info']
                    )->toHtml();

                    if ($item->super_user) {
                        $action = Html::link(
                            route('users.remove-super', $item->getKey()),
                            trans('core/acl::users.remove_super'),
                            ['class' => 'btn btn-danger']
                        )->toHtml();
                    }
                }

                return apply_filters(
                    ACL_FILTER_USER_TABLE_ACTIONS,
                    $action . view('core/acl::users.partials.actions', ['item' => $item])->render(),
                    $item
                );
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->leftJoin('role_users', 'users.id', '=', 'role_users.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'role_users.role_id')
            ->select([
                'users.id as id',
                'username',
                'email',
                'roles.name as role_name',
                'roles.id as role_id',
                'users.updated_at as updated_at',
                'users.created_at as created_at',
                'super_user',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'username' => [
                'title' => trans('core/acl::users.username'),
                'class' => 'text-start',
            ],
            'email' => [
                'title' => trans('core/acl::users.email'),
                'class' => 'text-start',
            ],
            'role_name' => [
                'title' => trans('core/acl::users.role'),
                'searchable' => false,
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name' => 'users.updated_at',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
            'super_user' => [
                'title' => trans('core/acl::users.is_super'),
                'width' => '100px',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('users.create'), 'users.create');
    }

    public function htmlDrawCallbackFunction(): string|null
    {
        return parent::htmlDrawCallbackFunction() . '$(".editable").editable({mode: "inline"});';
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('users.deletes'), 'users.destroy', parent::bulkActions());
    }

    public function getFilters(): array
    {
        $filters = $this->getBulkChanges();
        Arr::forget($filters, 'status');

        return $filters;
    }

    public function getBulkChanges(): array
    {
        return [
            'username' => [
                'title' => trans('core/acl::users.username'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'email' => [
                'title' => trans('core/base::tables.email'),
                'type' => 'text',
                'validate' => 'required|max:120|email',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'customSelect',
                'choices' => UserStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', UserStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }

    public function getOperationsHeading(): array
    {
        return [
            'operations' => [
                'title' => trans('core/base::tables.operations'),
                'width' => '350px',
                'class' => 'text-end',
                'orderable' => false,
                'searchable' => false,
                'exportable' => false,
                'printable' => false,
            ],
        ];
    }

    public function saveBulkChanges(array $ids, string $inputKey, string|null $inputValue): bool
    {
        if (BaseHelper::hasDemoModeEnabled()) {
            throw new DisabledInDemoModeException();
        }

        if ($inputKey === 'status') {
            $hasWarning = false;

            foreach ($ids as $id) {
                if ($inputValue == UserStatusEnum::DEACTIVATED && Auth::id() == $id) {
                    $hasWarning = true;
                }

                /**
                 * @var User $user
                 */
                $user = $this->getModel()->query()->findOrFail($id);

                if ($inputValue == UserStatusEnum::ACTIVATED) {
                    $this->service->activate($user);
                } else {
                    $this->service->remove($user);
                }

                event(new UpdatedContentEvent(USER_MODULE_SCREEN_NAME, request(), $user));
            }

            if ($hasWarning) {
                throw new Exception(trans('core/acl::users.lock_user_logged_in'));
            }

            return true;
        }

        return parent::saveBulkChanges($ids, $inputKey, $inputValue);
    }
}
