<?php

return [
    // List supported modules or plugins
    'supported' => [
        'Botble\Gallery\Models\Gallery',
        'Botble\Page\Models\Page',
        'Botble\Blog\Models\Post',
    ],
    'use_language_v2' => env('GALLERY_USE_LANGUAGE_VERSION_2', false),
];
