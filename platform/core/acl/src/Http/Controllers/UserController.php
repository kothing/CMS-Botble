<?php

namespace Botble\ACL\Http\Controllers;

use Botble\ACL\Forms\PasswordForm;
use Botble\ACL\Forms\ProfileForm;
use Botble\ACL\Forms\UserForm;
use Botble\ACL\Http\Requests\AvatarRequest;
use Botble\ACL\Http\Requests\CreateUserRequest;
use Botble\ACL\Http\Requests\UpdatePasswordRequest;
use Botble\ACL\Http\Requests\UpdateProfileRequest;
use Botble\ACL\Models\User;
use Botble\ACL\Models\UserMeta;
use Botble\ACL\Services\ChangePasswordService;
use Botble\ACL\Services\CreateUserService;
use Botble\ACL\Tables\UserTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Models\MediaFile;
use Botble\Media\Services\ThumbnailService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Throwable;

class UserController extends BaseController
{
    public function index(UserTable $dataTable)
    {
        PageTitle::setTitle(trans('core/acl::users.users'));

        Assets::addScripts(['bootstrap-editable', 'jquery-ui'])
            ->addStyles(['bootstrap-editable']);

        return $dataTable->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/acl::users.create_new_user'));

        return $formBuilder->create(UserForm::class)->renderForm();
    }

    public function store(CreateUserRequest $request, CreateUserService $service, BaseHttpResponse $response)
    {
        $user = $service->execute($request);

        event(new CreatedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));

        return $response
            ->setPreviousUrl(route('users.index'))
            ->setNextUrl(route('users.profile.view', $user->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        $user = User::query()->findOrFail($id);

        if ($request->user()->is($user)) {
            return $response
                ->setError()
                ->setMessage(trans('core/acl::users.delete_user_logged_in'));
        }

        try {
            if (! $request->user()->isSuperUser() && $user->isSuperUser()) {
                return $response
                    ->setError()
                    ->setMessage(trans('core/acl::users.cannot_delete_super_user'));
            }

            $user->delete();

            event(new DeletedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));

            return $response->setMessage(trans('core/acl::users.deleted'));
        } catch (Exception) {
            return $response
                ->setError()
                ->setMessage(trans('core/acl::users.cannot_delete'));
        }
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
            if ($request->user()->getKey() == $id) {
                return $response
                    ->setError()
                    ->setMessage(trans('core/acl::users.delete_user_logged_in'));
            }

            try {
                $user = User::query()->findOrFail($id);
                if (! $request->user()->isSuperUser() && $user->isSuperUser()) {
                    continue;
                }

                $user->delete();

                event(new DeletedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));
            } catch (Exception $exception) {
                return $response
                    ->setError()
                    ->setMessage($exception->getMessage());
            }
        }

        return $response->setMessage(trans('core/acl::users.deleted'));
    }

    public function getUserProfile(int|string $id, Request $request, FormBuilder $formBuilder)
    {
        $user = User::query()->findOrFail($id);

        Assets::addScripts(['bootstrap-pwstrength', 'cropper'])
            ->addScriptsDirectly('vendor/core/core/acl/js/profile.js');

        PageTitle::setTitle(trans(':name', ['name' => $user->name]));

        $form = $formBuilder
            ->create(ProfileForm::class, ['model' => $user])
            ->setUrl(route('users.update-profile', $user->getKey()));

        $passwordForm = $formBuilder
            ->create(PasswordForm::class)
            ->setUrl(route('users.change-password', $user->getKey()));

        $currentUser = $request->user();

        $canChangeProfile = $currentUser->hasPermission('users.edit') || $currentUser->getKey(
        ) == $user->getKey() || $currentUser->isSuperUser();

        if (! $canChangeProfile) {
            $form->disableFields();
            $form->removeActionButtons();
            $form->setActionButtons(' ');
            $passwordForm->disableFields();
            $passwordForm->removeActionButtons();
            $passwordForm->setActionButtons(' ');
        }

        if ($currentUser->isSuperUser()) {
            $passwordForm->remove('old_password');
        }

        $form = $form->renderForm();
        $passwordForm = $passwordForm->renderForm();

        return view('core/acl::users.profile.base', compact('user', 'form', 'passwordForm', 'canChangeProfile'));
    }

    public function postUpdateProfile(int|string $id, UpdateProfileRequest $request, BaseHttpResponse $response)
    {
        $user = User::query()->findOrFail($id);

        $currentUser = $request->user();

        $hasRightToUpdate = $currentUser->hasPermission('users.edit') ||
            $currentUser->getKey() === $user->getKey() ||
            $currentUser->isSuperUser();

        if (! $hasRightToUpdate) {
            return $response
                ->setNextUrl(route('users.profile.view', $user->getKey()))
                ->setError()
                ->setMessage(trans('core/acl::permissions.access_denied_message'));
        }

        if ($user->email !== $request->input('email')) {
            $users = User::query()
                ->where('email', $request->input('email'))
                ->where('id', '<>', $user->getKey())
                ->exists();

            if ($users) {
                return $response
                    ->setError()
                    ->setMessage(trans('core/acl::users.email_exist'))
                    ->withInput();
            }
        }

        if ($user->username !== $request->input('username')) {
            $users = User::query()
                ->getModel()
                ->where('username', $request->input('username'))
                ->where('id', '<>', $user->getKey())
                ->exists();

            if ($users) {
                return $response
                    ->setError()
                    ->setMessage(trans('core/acl::users.username_exist'))
                    ->withInput();
            }
        }

        $user->fill($request->input());
        $user->save();

        do_action(USER_ACTION_AFTER_UPDATE_PROFILE, USER_MODULE_SCREEN_NAME, $request, $user);

        event(new UpdatedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));

        return $response->setMessage(trans('core/acl::users.update_profile_success'));
    }

    public function postChangePassword(
        int|string $id,
        UpdatePasswordRequest $request,
        ChangePasswordService $service,
        BaseHttpResponse $response
    ) {
        $user = User::query()->findOrFail($id);

        $currentUser = $request->user();

        $hasRightToUpdate = $currentUser->hasPermission('users.edit') ||
            $currentUser->getKey() === $user->getKey() ||
            $currentUser->isSuperUser();

        if (! $hasRightToUpdate) {
            return $response
                ->setNextUrl(route('users.profile.view', $user->getKey()))
                ->setError()
                ->setMessage(trans('core/acl::permissions.access_denied_message'));
        }

        $request->merge(['id' => $user->getKey()]);

        try {
            $service->execute($request);
        } catch (Throwable $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }

        return $response->setMessage(trans('core/acl::users.password_update_success'));
    }

    public function postAvatar(
        int|string $id,
        AvatarRequest $request,
        ThumbnailService $thumbnailService,
        BaseHttpResponse $response
    ) {
        $user = User::query()->findOrFail($id);

        $currentUser = $request->user();

        $hasRightToUpdate = ($currentUser->hasPermission('users.edit') && $currentUser->getKey(
        ) === $user->getKey()) ||
            $currentUser->isSuperUser();

        if (! $hasRightToUpdate) {
            return $response
                ->setNextUrl(route('users.profile.view', $user->getKey()))
                ->setError()
                ->setMessage(trans('core/acl::permissions.access_denied_message'));
        }

        try {
            $result = RvMedia::handleUpload($request->file('avatar_file'), 0, 'users');

            if ($result['error']) {
                return $response->setError()->setMessage($result['message']);
            }

            $avatarData = json_decode($request->input('avatar_data'));

            $file = $result['data'];

            $thumbnailService
                ->setImage(RvMedia::getRealPath($file->url))
                ->setSize((int)$avatarData->width ?: 150, (int)$avatarData->height ?: 150)
                ->setCoordinates((int)$avatarData->x, (int)$avatarData->y)
                ->setDestinationPath(File::dirname($file->url))
                ->setFileName(File::name($file->url) . '.' . File::extension($file->url))
                ->save('crop');

            $mediaFile = MediaFile::query()->find($user->avatar_id);

            if ($mediaFile) {
                $mediaFile->delete();
            }

            $user->avatar_id = $file->id;
            $user->save();

            return $response
                ->setMessage(trans('core/acl::users.update_avatar_success'))
                ->setData(['url' => RvMedia::url($file->url)]);
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getTheme(string $theme)
    {
        if (Auth::check() && ! BaseHelper::hasDemoModeEnabled()) {
            UserMeta::setMeta('admin-theme', $theme);
        }

        session()->put('admin-theme', $theme);

        try {
            return redirect()->back();
        } catch (Exception) {
            return redirect()->route('access.login');
        }
    }

    public function makeSuper(int|string $id, BaseHttpResponse $response)
    {
        try {
            $user = User::query()->findOrFail($id);

            $user->updatePermission(ACL_ROLE_SUPER_USER, true);
            $user->updatePermission(ACL_ROLE_MANAGE_SUPERS, true);
            $user->super_user = 1;
            $user->manage_supers = 1;
            $user->save();

            return $response
                ->setNextUrl(route('users.index'))
                ->setMessage(trans('core/base::system.supper_granted'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setNextUrl(route('users.index'))
                ->setMessage($exception->getMessage());
        }
    }

    public function removeSuper(int|string $id, Request $request, BaseHttpResponse $response)
    {
        $user = User::query()->findOrFail($id);

        if ($request->user()->is($user)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::system.cannot_revoke_yourself'));
        }

        $user->updatePermission(ACL_ROLE_SUPER_USER, false);
        $user->updatePermission(ACL_ROLE_MANAGE_SUPERS, false);
        $user->super_user = 0;
        $user->manage_supers = 0;
        $user->save();

        return $response
            ->setNextUrl(route('users.index'))
            ->setMessage(trans('core/base::system.supper_revoked'));
    }

    public function toggleSidebarMenu(Request $request, BaseHttpResponse $response)
    {
        $status = $request->input('status') == 'true';

        session()->put('sidebar-menu-toggle', $status ? Carbon::now() : '');

        return $response;
    }
}
