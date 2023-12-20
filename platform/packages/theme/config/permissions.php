<?php

return [
    [
        'name' => 'Appearance',
        'flag' => 'core.appearance',
    ],
    [
        'name' => 'Theme',
        'flag' => 'theme.index',
        'parent_flag' => 'core.appearance',
    ],
    [
        'name' => 'Activate',
        'flag' => 'theme.activate',
        'parent_flag' => 'theme.index',
    ],
    [
        'name' => 'Remove',
        'flag' => 'theme.remove',
        'parent_flag' => 'theme.index',
    ],
    [
        'name' => 'Theme options',
        'flag' => 'theme.options',
        'parent_flag' => 'core.appearance',
    ],
    [
        'name' => 'Custom CSS',
        'flag' => 'theme.custom-css',
        'parent_flag' => 'core.appearance',
    ],
    [
        'name' => 'Custom JS',
        'flag' => 'theme.custom-js',
        'parent_flag' => 'core.appearance',
    ],
    [
        'name' => 'Custom HTML',
        'flag' => 'theme.custom-html',
        'parent_flag' => 'core.appearance',
    ],
];
