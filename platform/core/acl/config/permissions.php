<?php

return [
    [
        'name' => 'Users',
        'flag' => 'users.index',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Create',
        'flag' => 'users.create',
        'parent_flag' => 'users.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'users.edit',
        'parent_flag' => 'users.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'users.destroy',
        'parent_flag' => 'users.index',
    ],

    [
        'name' => 'Roles',
        'flag' => 'roles.index',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Create',
        'flag' => 'roles.create',
        'parent_flag' => 'roles.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'roles.edit',
        'parent_flag' => 'roles.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'roles.destroy',
        'parent_flag' => 'roles.index',
    ],
];
