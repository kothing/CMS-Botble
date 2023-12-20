<?php

return [
    [
        'name' => 'Block',
        'flag' => 'block.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'block.create',
        'parent_flag' => 'block.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'block.edit',
        'parent_flag' => 'block.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'block.destroy',
        'parent_flag' => 'block.index',
    ],
];
