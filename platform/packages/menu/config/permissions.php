<?php

return [
    [
        'name' => 'Menu',
        'flag' => 'menus.index',
        'parent_flag' => 'core.appearance',
    ],
    [
        'name' => 'Create',
        'flag' => 'menus.create',
        'parent_flag' => 'menus.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'menus.edit',
        'parent_flag' => 'menus.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'menus.destroy',
        'parent_flag' => 'menus.index',
    ],
];
