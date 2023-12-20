<?php

namespace Botble\ACL\Services;

use Botble\ACL\Models\User;
use Botble\Support\Services\ProduceServiceInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class ChangePasswordService implements ProduceServiceInterface
{
    public function execute(Request $request): bool|User
    {
        $currentUser = $request->user();

        if (! $currentUser->isSuperUser()) {
            if (! Hash::check($request->input('old_password'), $currentUser->getAuthPassword())) {
                throw new Exception(trans('core/acl::users.current_password_not_valid'));
            }
        }

        /**
         * @var User $user
         */
        $user = User::query()->findOrFail($request->input('id', $currentUser->getKey()));

        $password = $request->input('password');

        $user->password = Hash::make($password);
        $user->save();

        if ($user->id != $currentUser->getKey()) {
            try {
                Auth::setUser($user);
                Auth::logoutOtherDevices($password);
            } catch (Throwable $exception) {
                info($exception->getMessage());
            }
        }

        do_action(USER_ACTION_AFTER_UPDATE_PASSWORD, USER_MODULE_SCREEN_NAME, $request, $user);

        return $user;
    }
}
