<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Theme Default
    |--------------------------------------------------------------------------
    |
    | If you don't set a theme when using a "Theme" class the default theme
    | will replace automatically.
    |
    */

    'themeDefault' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Layout Default
    |--------------------------------------------------------------------------
    |
    | If you don't set a layout when using a "Theme" class the default layout
    | will replace automatically.
    |
    */

    'layoutDefault' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Path to lookup theme
    |--------------------------------------------------------------------------
    |
    | The root path contains themes collections.
    |
    */

    'themeDir' => 'themes',

    /*
    |--------------------------------------------------------------------------
    | A pieces of theme collections
    |--------------------------------------------------------------------------
    |
    | Inside a theme path we need to set up directories to
    | keep "layouts", "assets" and "partials".
    |
    */

    'containerDir' => [
        'layout' => 'layouts',
        'asset' => '',
        'partial' => 'partials',
        'view' => 'views',
    ],

    /*
    |--------------------------------------------------------------------------
    | Listener from events
    |--------------------------------------------------------------------------
    |
    | You can hook a theme when event fired on activities
    | this is cool feature to set up a title, meta, default styles and scripts.
    |
    */

    'events' => [],

    'enable_custom_js' => env('CMS_THEME_ENABLE_CUSTOM_JS', true),

    'enable_custom_html' => env('CMS_THEME_ENABLE_CUSTOM_HTML', true),

    'enable_custom_html_shortcode' => env('CMS_THEME_ENABLE_CUSTOM_HTML_SHORTCODE', true),

    'public_theme_name' => env('CMS_THEME_PUBLIC_NAME'),

    'display_theme_manager_in_admin_panel' => env('CMS_THEME_DISPLAY_THEME_MANAGER_IN_ADMIN_PANEL', true),

    'public_single_ending_url' => env('PUBLIC_SINGLE_ENDING_URL'),
];
