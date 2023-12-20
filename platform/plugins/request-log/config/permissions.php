<?php

return [
    [
        'name' => 'Request Logs',
        'flag' => 'request-log.index',
        'parent_flag' => 'core.system',
    ],
    [
        'name' => 'Delete',
        'flag' => 'request-log.destroy',
        'parent_flag' => 'request-log.index',
    ],
];
