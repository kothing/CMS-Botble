<?php

return [
    [
        'name' => 'Activity Logs',
        'flag' => 'audit-log.index',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Delete',
        'flag' => 'audit-log.destroy',
        'parent_flag' => 'audit-log.index',
    ],
];
