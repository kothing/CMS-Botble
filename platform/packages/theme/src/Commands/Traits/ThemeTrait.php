<?php

namespace Botble\Theme\Commands\Traits;

trait ThemeTrait
{
    protected function getPath(string|null $path = null, string|null $theme = null): string
    {
        $rootPath = theme_path();
        if ($this->option('path')) {
            $rootPath = $this->option('path');
        }

        if (! $theme) {
            $theme = $this->getTheme();
        }

        return rtrim($rootPath, '/') . '/' . rtrim(ltrim(strtolower($theme), '/'), '/') . '/' . $path;
    }

    protected function getTheme(): string
    {
        if ($this->hasArgument('name')) {
            return strtolower($this->argument('name'));
        }

        return strtolower($this->option('name'));
    }
}
