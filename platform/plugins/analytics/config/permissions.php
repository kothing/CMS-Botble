<?php

return [
    [
        'name' => 'Analytics',
        'flag' => 'analytics.general',
    ],
    [
        'name' => 'Top Page',
        'flag' => 'analytics.page',
        'parent_flag' => 'analytics.general',
    ],
    [
        'name' => 'Top Browser',
        'flag' => 'analytics.browser',
        'parent_flag' => 'analytics.general',
    ],
    [
        'name' => 'Top Referrer',
        'flag' => 'analytics.referrer',
        'parent_flag' => 'analytics.general',
    ],
];
