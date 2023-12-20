<?php

namespace Botble\Theme\Commands;

use Botble\Setting\Facades\Setting;
use Botble\Theme\Commands\Traits\ThemeTrait;
use Botble\Theme\Facades\ThemeOption;
use Botble\Theme\Services\ThemeService;
use Botble\Widget\Models\Widget;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:theme:rename', 'Rename theme to the new name')]
class ThemeRenameCommand extends Command
{
    use ThemeTrait;

    public function handle(File $files, ThemeService $themeService): int
    {
        $theme = $this->getTheme();

        $newName = $this->argument('newName');

        if ($theme == $newName) {
            $this->components->error('Theme name are the same!');

            return self::FAILURE;
        }

        if ($files->isDirectory(theme_path($newName))) {
            $this->components->error("Theme <info>$theme</info> is already exists.");

            return self::FAILURE;
        }

        $files->move(theme_path($theme), theme_path($newName));

        $themeService->activate($newName);

        $themeOptions = Setting::newQuery()->where('key', 'LIKE', ThemeOption::getOptionKey('%', null, $theme))->get();

        foreach ($themeOptions as $option) {
            $option->key = str_replace(ThemeOption::getOptionKey('', theme: $theme), ThemeOption::getOptionKey('', theme: $newName), $option->key);
            $option->save();
        }

        Widget::query()->where('theme', $theme)->update(['theme' => $newName]);

        $widgets = Widget::query()->where('theme', 'LIKE', $theme . '-%')->get();

        foreach ($widgets as $widget) {
            $widget->theme = str_replace($theme, $newName, $widget->theme);
            $widget->save();
        }

        $this->components->info("Theme <info>$theme</info> has been renamed to <info>$newName</info>!");

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The theme name that you want to rename');
        $this->addArgument('newName', InputArgument::REQUIRED, 'The new name');
    }
}
