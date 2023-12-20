<?php

return [
    [
        'name' => 'Backup',
        'flag' => 'backups.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'backups.create',
        'parent_flag' => 'backups.index',
    ],
    [
        'name' => 'Restore',
        'flag' => 'backups.restore',
        'parent_flag' => 'backups.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'backups.destroy',
        'parent_flag' => 'backups.index',
    ],
];
