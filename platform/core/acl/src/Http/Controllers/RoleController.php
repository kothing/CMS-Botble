<?php

namespace Botble\ACL\Http\Controllers;

use Botble\ACL\Events\RoleAssignmentEvent;
use Botble\ACL\Events\RoleUpdateEvent;
use Botble\ACL\Forms\RoleForm;
use Botble\ACL\Http\Requests\AssignRoleRequest;
use Botble\ACL\Http\Requests\RoleCreateRequest;
use Botble\ACL\Models\Role;
use Botble\ACL\Models\User;
use Botble\ACL\Tables\RoleTable;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Helper;
use Illuminate\Http\Request;

class RoleController extends BaseController
{
    public function index(RoleTable $dataTable)
    {
        PageTitle::setTitle(trans('core/acl::permissions.role_permission'));

        return $dataTable->renderTable();
    }

    public function destroy(int|string $id, BaseHttpResponse $response)
    {
        $role = Role::query()->findOrFail($id);

        $role->delete();

        Helper::clearCache();

        return $response->setMessage(trans('core/acl::permissions.delete_success'));
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $role = Role::query()->findOrFail($id);
            $role->delete();
        }

        Helper::clearCache();

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $role = Role::query()->findOrFail($id);

        PageTitle::setTitle(trans('core/acl::permissions.details') . ' - ' . BaseHelper::clean($role->name));

        return $formBuilder->create(RoleForm::class, ['model' => $role])->renderForm();
    }

    public function update(int|string $id, RoleCreateRequest $request, BaseHttpResponse $response)
    {
        $role = Role::query()->findOrFail($id);

        if ($request->input('is_default')) {
            Role::query()->getModel()->where('id', '!=', $role->getKey())->update(['is_default' => 0]);
        }

        $role->name = $request->input('name');
        $role->permissions = $this->cleanPermission((array)$request->input('flags', []));
        $role->description = $request->input('description');
        $role->updated_by = $request->user()->getKey();
        $role->is_default = $request->input('is_default');
        $role->save();

        Helper::clearCache();

        event(new RoleUpdateEvent($role));

        return $response
            ->setPreviousUrl(route('roles.index'))
            ->setNextUrl(route('roles.edit', $role->getKey()))
            ->setMessage(trans('core/acl::permissions.modified_success'));
    }

    protected function cleanPermission(array $permissions): array
    {
        if (! $permissions) {
            return [];
        }

        $cleanedPermissions = [];
        foreach ($permissions as $permissionName) {
            $cleanedPermissions[$permissionName] = true;
        }

        return $cleanedPermissions;
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/acl::permissions.create_role'));

        return $formBuilder->create(RoleForm::class)->renderForm();
    }

    public function store(RoleCreateRequest $request, BaseHttpResponse $response)
    {
        if ($request->input('is_default')) {
            Role::query()->getModel()->where('id', '>', 0)->update(['is_default' => 0]);
        }

        $role = Role::query()->create([
            'name' => $request->input('name'),
            'permissions' => $this->cleanPermission((array)$request->input('flags', [])),
            'description' => $request->input('description'),
            'is_default' => $request->input('is_default'),
            'created_by' => $request->user()->getKey(),
            'updated_by' => $request->user()->getKey(),
        ]);

        return $response
            ->setPreviousUrl(route('roles.index'))
            ->setNextUrl(route('roles.edit', $role->getKey()))
            ->setMessage(trans('core/acl::permissions.create_success'));
    }

    public function getDuplicate(int|string $id, BaseHttpResponse $response)
    {
        $baseRole = Role::query()->findOrFail($id);

        $role = Role::query()->create([
            'name' => $baseRole->name . ' (Duplicate)',
            'slug' => $baseRole->slug,
            'permissions' => $baseRole->permissions,
            'description' => $baseRole->description,
            'created_by' => $baseRole->created_by,
            'updated_by' => $baseRole->updated_by,
        ]);

        return $response
            ->setPreviousUrl(route('roles.edit', $baseRole->getKey()))
            ->setNextUrl(route('roles.edit', $role->getKey()))
            ->setMessage(trans('core/acl::permissions.duplicated_success'));
    }

    public function getJson(): array
    {
        $pl = [];
        foreach (Role::query()->get() as $role) {
            $pl[] = [
                'value' => $role->getKey(),
                'text' => $role->name,
            ];
        }

        return $pl;
    }

    public function postAssignMember(AssignRoleRequest $request, BaseHttpResponse $response): BaseHttpResponse
    {
        $user = User::query()->findOrFail($request->input('pk'));
        $role = Role::query()->findOrFail($request->input('value'));

        $user->roles()->sync([$role->getKey()]);

        event(new RoleAssignmentEvent($role, $user));

        return $response;
    }
}
