<?php

namespace Botble\DevTool\Commands;

use Botble\ACL\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand('cms:user:rebuild-permissions', 'Rebuild all the user permissions from the users defined roles and the roles defined flags')]
class RebuildPermissionsCommand extends Command
{
    public function handle(): int
    {
        try {
            // Safety first!
            DB::beginTransaction();

            // Firstly, lets grab out the global roles
            $allRoles = DB::select('SELECT id, name, permissions FROM roles');

            if (empty($allRoles)) {
                $users = User::query()->get();
                foreach ($users as $user) {
                    $user->permissions = [
                        ACL_ROLE_SUPER_USER => (bool)$user->super_user,
                        ACL_ROLE_MANAGE_SUPERS => (bool)$user->manage_supers,
                    ];
                    $user->save();
                }
            } else {
                // Go and grab all the permission flags defined on these global roles
                foreach ($allRoles as $role) {
                    $permissions = json_decode($role->permissions ?: '[]');

                    $userRoles = DB::select('SELECT user_id, role_id FROM role_users WHERE role_id=' . $role->id);
                    foreach ($userRoles as $userRole) {
                        $user = DB::select(
                            'SELECT super_user, manage_supers FROM users WHERE id=' . $userRole->user_id
                        );
                        if (! empty($user)) {
                            $user = $user[0];
                            $permissions[ACL_ROLE_SUPER_USER] = (bool)$user->super_user;
                            $permissions[ACL_ROLE_MANAGE_SUPERS] = (bool)$user->manage_supers;
                            DB::statement(
                                "UPDATE users SET permissions = '" . json_encode(
                                    $permissions
                                ) . "' where id=" . $userRole->user_id
                            );
                        }
                    }
                }
            }

            $this->components->info('Rebuild user permissions successfully!');

            DB::commit();

            return self::SUCCESS;
        } catch (Throwable $exception) {
            DB::rollBack();

            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
