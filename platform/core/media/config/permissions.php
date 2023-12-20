<?php

return [
    [
        'name' => 'Media',
        'flag' => 'media.index',
    ],
    [
        'name' => 'File',
        'flag' => 'files.index',
        'parent_flag' => 'media.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'files.create',
        'parent_flag' => 'files.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'files.edit',
        'parent_flag' => 'files.index',
    ],
    [
        'name' => 'Trash',
        'flag' => 'files.trash',
        'parent_flag' => 'files.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'files.destroy',
        'parent_flag' => 'files.index',
    ],

    [
        'name' => 'Folder',
        'flag' => 'folders.index',
        'parent_flag' => 'media.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'folders.create',
        'parent_flag' => 'folders.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'folders.edit',
        'parent_flag' => 'folders.index',
    ],
    [
        'name' => 'Trash',
        'flag' => 'folders.trash',
        'parent_flag' => 'folders.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'folders.destroy',
        'parent_flag' => 'folders.index',
    ],
];
