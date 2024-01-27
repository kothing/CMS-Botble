<?php

use Botble\Theme\Theme;

return [

    /*
    |--------------------------------------------------------------------------
    | Inherit from another theme
    |--------------------------------------------------------------------------
    |
    | Set up inherit from another if the file is not exists,
    | this is work with "layouts", "partials" and "views"
    |
    | [Notice] assets cannot inherit.
    |
    */

    'inherit' => null, //default

    /*
    |--------------------------------------------------------------------------
    | Listener from events
    |--------------------------------------------------------------------------
    |
    | You can hook a theme when event fired on activities
    | this is cool feature to set up a title, meta, default styles and scripts.
    |
    | [Notice] these event can be override by package config.
    |
    */

    'events' => [

        // Before event inherit from package config and the theme that call before,
        // you can use this event to set meta, breadcrumb template or anything
        // you want inheriting.
        'before' => function ($theme) {
            // You can remove this line anytime.
        },

        // Listen on event before render a theme,
        // this event should call to assign some assets,
        // breadcrumb template.
        'beforeRenderTheme' => function (Theme $theme) {
            // Partial composer.
            // $theme->partialComposer('header', function($view) {
            //     $view->with('auth', \Auth::user());
            // });

            $version = get_cms_version();

            // You may use this event to set up your assets.
            $theme->asset()->usePath()->add('style', 'css/style.css', [], [], $version);
            $theme->asset()->usePath()->add('widgets', 'css/widgets.css', [], [], $version);
            $theme->asset()->usePath()->add('responsive', 'css/responsive.css', [], [], $version);
            $theme->asset()->usePath()->add('custom', 'css/custom.css', [], [], $version);

            if (BaseHelper::siteLanguageDirection() == 'rtl') {
                $theme->asset()->usePath()->add('rtl', 'css/rtl.css', [], [], $version);
            }

            $theme->asset()->container('footer')->usePath()->add('modernizr', 'js/vendor/modernizr-3.5.0.min.js');
            $theme->asset()->container('footer')->usePath()->add('jquery', 'js/vendor/jquery-3.5.1.min.js');
            $theme->asset()->container('footer')->usePath()->add('jquery.slicknav', 'js/vendor/jquery.slicknav.js', ['jquery'], [], '1.0.11');
            $theme->asset()->container('footer')->usePath()->add('slick-js', 'js/vendor/slick.min.js');
            $theme->asset()->container('footer')->usePath()->add('wow-js', 'js/vendor/wow.min.js');
            $theme->asset()->container('footer')->usePath()->add('jquery.ticker', 'js/vendor/jquery.ticker.js');
            $theme->asset()->container('footer')->usePath()->add('jquery.vticker', 'js/vendor/jquery.vticker-min.js');
            $theme->asset()->container('footer')->usePath()->add('jquery.scrollUp', 'js/vendor/jquery.scrollUp.min.js');
            $theme->asset()->container('footer')->usePath()->add('jquery.nice-select', 'js/vendor/jquery.nice-select.min.js');
            $theme->asset()->container('footer')->usePath()->add('jquery.magnific-popup', 'js/vendor/jquery.magnific-popup.js');
            $theme->asset()->container('footer')->usePath()->add('jquery.sticky', 'js/vendor/jquery.sticky.js');
            $theme->asset()->container('footer')->usePath()->add('perfect-scrollbar', 'js/vendor/perfect-scrollbar.js');
            $theme->asset()->container('footer')->usePath()->add('waypoints', 'js/vendor/jquery.waypoints.min.js');
            $theme->asset()->container('footer')->usePath()->add('jquery.theia.sticky', 'js/vendor/jquery.theia.sticky.js');

            $theme->asset()->container('footer')->usePath()->add('main', 'js/main.js', ['jquery'], [], $version);
            $theme->asset()->container('footer')->usePath()->add('backend', 'js/backend.js', ['jquery'], [], $version);

            if (function_exists('shortcode')) {
                $theme->composer(['page', 'post'], function (\Botble\Shortcode\View\View $view) {
                    $view->withShortcodes();
                });
            }
        },

        // Listen on event before render a layout,
        // this should call to assign style, script for a layout.
        'beforeRenderLayout' => [

            'default' => function ($theme) {
                // $theme->asset()->usePath()->add('ipad', 'css/layouts/ipad.css');
            },
        ],
    ],
];
