<?php

return [
    [
        'name' => 'Galleries',
        'flag' => 'galleries.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'galleries.create',
        'parent_flag' => 'galleries.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'galleries.edit',
        'parent_flag' => 'galleries.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'galleries.destroy',
        'parent_flag' => 'galleries.index',
    ],
];
