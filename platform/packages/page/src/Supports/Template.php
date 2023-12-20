<?php

namespace Botble\Page\Supports;

use Botble\Base\Facades\BaseHelper;
use Botble\Theme\Facades\Theme;

class Template
{
    public static function registerPageTemplate(array $templates = []): void
    {
        $validTemplates = [];
        foreach ($templates as $key => $template) {
            if (in_array($key, self::getExistsTemplate())) {
                $validTemplates[$key] = $template;
            }
        }

        config([
            'packages.page.general.templates' => array_merge(
                config('packages.page.general.templates'),
                $validTemplates
            ),
        ]);
    }

    protected static function getExistsTemplate(): array
    {
        $files = BaseHelper::scanFolder(theme_path(Theme::getThemeName() . DIRECTORY_SEPARATOR . config('packages.theme.general.containerDir.layout')));
        foreach ($files as $key => $file) {
            $files[$key] = str_replace('.blade.php', '', $file);
        }

        return $files;
    }

    public static function getPageTemplates(): array
    {
        return (array)config('packages.page.general.templates', []);
    }
}
