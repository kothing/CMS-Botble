<?php

return [
    [
        'name' => 'Translation',
        'flag' => 'plugins.translation',
    ],
    [
        'name' => 'Locales',
        'flag' => 'translations.locales',
        'parent_flag' => 'plugins.translation',
    ],
    [
        'name' => 'Theme translations',
        'flag' => 'translations.theme-translations',
        'parent_flag' => 'plugins.translation',
    ],
    [
        'name' => 'Other translations',
        'flag' => 'translations.index',
        'parent_flag' => 'plugins.translation',
    ],
];
