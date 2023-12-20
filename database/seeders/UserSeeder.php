<?php

namespace Database\Seeders;

use Botble\ACL\Models\Role;
use Botble\ACL\Models\User;
use Botble\ACL\Services\ActivateUserService;
use Botble\Base\Supports\BaseSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends BaseSeeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        User::query()->truncate();
        Role::query()->truncate();
        DB::table('role_users')->truncate();
        DB::table('activations')->truncate();

        $permissions = (new Role())->getAvailablePermissions();

        $permissions = array_map(function () {
            return true;
        }, $permissions);

        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Admin users role',
                'permissions' => $permissions,
                'is_default' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($roles as $item) {
            $role = new Role();
            $role->forceFill($item);
            $role->save();
        }

        $users = [
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@botble.com',
                'username' => 'botble',
                'password' => Hash::make('159357'),
                'super_user' => 1,
                'manage_supers' => 1,
            ],
            [
                'first_name' => 'Normal',
                'last_name' => 'Admin',
                'email' => 'user@botble.com',
                'username' => 'admin',
                'password' => Hash::make('12345678'),
                'permissions' => $permissions,
                'role_id' => 1,
            ],
        ];

        $activateUserService = app(ActivateUserService::class);

        foreach ($users as $item) {
            $user = new User();
            $user->forceFill(Arr::except($item, ['role_id']));
            $user->save();

            $activateUserService->activate($user);

            if (isset($item['role_id'])) {
                $user->roles()->attach($item['role_id']);
            }
        }
    }
}
