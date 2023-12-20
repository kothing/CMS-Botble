<?php

use Botble\Theme\Contracts\Theme as ThemeContract;
use Botble\Theme\Facades\AdminBar;
use Botble\Theme\Facades\ThemeOption;
use Botble\Theme\Supports\AdminBar as AdminBarBase;
use Botble\Theme\ThemeOption as ThemeOptionBase;

if (! function_exists('sanitize_html_class')) {
    function sanitize_html_class(string $class, string|callable|null $fallback = ''): string
    {
        //Strip out any % encoded octets
        $sanitized = preg_replace('|%[a-fA-F0-9][a-fA-F0-9]|', '', $class);

        //Limit to A-Z,a-z,0-9,_,-
        $sanitized = preg_replace('/[^A-Za-z0-9_-]/', '', $sanitized);

        if ('' == $sanitized && $fallback) {
            return sanitize_html_class($fallback);
        }

        return apply_filters('sanitize_html_class', $sanitized, $class, $fallback);
    }
}

if (! function_exists('parse_args')) {
    function parse_args(array|object $args, string|array $defaults = ''): array
    {
        if (is_object($args)) {
            $result = get_object_vars($args);
        } else {
            $result =&$args;
        }

        if (is_array($defaults)) {
            return array_merge($defaults, $result);
        }

        return $result;
    }
}

if (! function_exists('theme')) {
    function theme(string|null $themeName = null, string|null $layoutName = null): mixed
    {
        $theme = app(ThemeContract::class);

        if ($themeName) {
            $theme->theme($themeName);
        }

        if ($layoutName) {
            $theme->layout($layoutName);
        }

        return $theme;
    }
}

if (! function_exists('theme_option')) {
    function theme_option($key = null, $default = ''): ThemeOptionBase|string|null
    {
        if (! empty($key)) {
            try {
                return ThemeOption::getOption($key, $default);
            } catch (Exception $exception) {
                info($exception->getMessage());

                return $default;
            }
        }

        return ThemeOption::getFacadeRoot();
    }
}

if (! function_exists('theme_path')) {
    function theme_path(string|null $path = null): string
    {
        return platform_path('themes' . DIRECTORY_SEPARATOR . $path);
    }
}

if (! function_exists('admin_bar')) {
    function admin_bar(): AdminBarBase
    {
        return AdminBar::getFacadeRoot();
    }
}
