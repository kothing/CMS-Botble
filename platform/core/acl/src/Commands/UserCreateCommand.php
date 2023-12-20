<?php

namespace Botble\ACL\Commands;

use Botble\ACL\Models\User;
use Botble\ACL\Services\ActivateUserService;
use Botble\Base\Commands\Traits\ValidateCommandInput;
use Botble\Base\Supports\Helper;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:user:create', 'Create a super user')]
class UserCreateCommand extends Command
{
    use ValidateCommandInput;

    public function handle(ActivateUserService $activateUserService): int
    {
        $this->components->info('Creating a super user...');

        try {
            $user = new User();
            $user->first_name = $this->askWithValidate('Enter first name', 'required|min:2|max:60');
            $user->last_name = $this->askWithValidate('Enter last name', 'required|min:2|max:60');
            $user->email = $this->askWithValidate('Enter email address', 'required|email|unique:users,email');
            $user->username = $this->askWithValidate('Enter username', 'required|min:4|max:60|unique:users,username');
            $user->password = Hash::make($this->askWithValidate('Enter password', 'required|min:6|max:60', true));
            $user->super_user = 1;
            $user->manage_supers = 1;
            $user->save();

            if ($activateUserService->activate($user)) {
                $this->components->info('Super user is created.');
            }

            Helper::clearCache();

            return self::SUCCESS;
        } catch (Exception $exception) {
            $this->components->error('User could not be created.');
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
