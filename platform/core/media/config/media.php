<?php

return [
    'sizes' => [
        'thumb' => '150x150',
    ],
    'permissions' => [
        'folders.create',
        'folders.edit',
        'folders.trash',
        'folders.destroy',
        'files.create',
        'files.edit',
        'files.trash',
        'files.destroy',
        'files.favorite',
        'folders.favorite',
    ],
    'libraries' => [
        'stylesheets' => [
            'vendor/core/core/media/libraries/jquery-context-menu/jquery.contextMenu.min.css',
            'vendor/core/core/media/css/media.css?v=' . time(),
        ],
        'javascript' => [
            'vendor/core/core/media/libraries/lodash/lodash.min.js',
            'vendor/core/core/media/libraries/clipboard/clipboard.min.js',
            'vendor/core/core/media/libraries/dropzone/dropzone.js',
            'vendor/core/core/media/libraries/jquery-context-menu/jquery.ui.position.min.js',
            'vendor/core/core/media/libraries/jquery-context-menu/jquery.contextMenu.min.js',
            'vendor/core/core/media/js/media.js?v=' . time(),
        ],
    ],
    'allowed_mime_types' => env(
        'RV_MEDIA_ALLOWED_MIME_TYPES',
        'jpg,jpeg,png,gif,txt,docx,zip,mp3,bmp,csv,xls,xlsx,ppt,pptx,pdf,mp4,doc,mpga,wav,webp,mov'
    ),
    'mime_types' => [
        'image' => [
            'image/png',
            'image/jpeg',
            'image/gif',
            'image/bmp',
            'image/svg+xml',
            'image/webp',
        ],
        'video' => [
            'video/mp4',
            'video/mov',
            'video/quicktime',
        ],
        'document' => [
            'application/pdf',
            'application/vnd.ms-excel',
            'application/excel',
            'application/x-excel',
            'application/x-msexcel',
            'text/plain',
            'application/msword',
            'text/csv',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ],
    ],
    'default_image' => env('RV_MEDIA_DEFAULT_IMAGE', '/vendor/core/core/base/images/placeholder.png'),
    'sidebar_display' => env('RV_MEDIA_SIDEBAR_DISPLAY', 'horizontal'), // Use "vertical" or "horizontal"
    'watermark' => [
        'enabled' => env('RV_MEDIA_WATERMARK_ENABLED', 0),
        'source' => env('RV_MEDIA_WATERMARK_SOURCE'),
        'size' => env('RV_MEDIA_WATERMARK_SIZE', 10),
        'opacity' => env('RV_MEDIA_WATERMARK_OPACITY', 70),
        'position' => env('RV_MEDIA_WATERMARK_POSITION', 'bottom-right'),
        'x' => env('RV_MEDIA_WATERMARK_X', 10),
        'y' => env('RV_MEDIA_WATERMARK_Y', 10),
    ],

    'chunk' => [
        'enabled' => env('RV_MEDIA_UPLOAD_CHUNK', false),
        'chunk_size' => 1024 * 1024, // Bytes
        'max_file_size' => 1024 * 1024, // MB

        /*
         * The storage config
         */
        'storage' => [
            /*
             * Returns the folder name of the chunks. The location is in storage/app/{folder_name}
             */
            'chunks' => 'chunks',
            'disk' => 'local',
        ],
        'clear' => [
            /*
             * How old chunks we should delete
             */
            'timestamp' => '-3 HOURS',
            'schedule' => [
                'enabled' => true,
                'cron' => '25 * * * *', // run every hour on the 25th minute
            ],
        ],
        'chunk' => [
            // setup for the chunk naming setup to ensure same name upload at same time
            'name' => [
                'use' => [
                    'session' => true, // should the chunk name use the session id? The uploader must send cookie!,
                    'browser' => false, // instead of session we can use the ip and browser?
                ],
            ],
        ],
    ],

    'preview' => [
        'document' => [
            'enabled' => env('RV_MEDIA_DOCUMENT_PREVIEW_ENABLED', true),
            'providers' => [
                'google' => 'https://docs.google.com/gview?embedded=true&url={url}',
                'microsoft' => 'https://view.officeapps.live.com/op/view.aspx?src={url}',
            ],
            'default' => env('RV_MEDIA_DOCUMENT_PREVIEW_PROVIDER', 'microsoft'),
            'type' => env('RV_MEDIA_DOCUMENT_PREVIEW_TYPE', 'iframe'),          // use iframe or popup
            'mime_types' => [
                'application/pdf',
                'application/vnd.ms-excel',
                'application/excel',
                'application/x-excel',
                'application/x-msexcel',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
        ],
    ],
    'default_upload_url' => env('RV_MEDIA_DEFAULT_UPLOAD_URL', url('storage')),
    'generate_thumbnails_enabled' => env('RV_MEDIA_GENERATE_THUMBNAILS_ENABLED', true),
];
