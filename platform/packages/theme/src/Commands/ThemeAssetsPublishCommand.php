<?php

namespace Botble\Theme\Commands;

use Botble\Theme\Commands\Traits\ThemeTrait;
use Botble\Theme\Services\ThemeService;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('cms:theme:assets:publish', 'Publish assets for a theme')]
class ThemeAssetsPublishCommand extends Command
{
    use ThemeTrait;

    public function handle(File $files, ThemeService $themeService): int
    {
        if ($this->option('name') && ! preg_match('/^[a-z0-9\-]+$/i', $this->option('name'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        if ($this->option('name') && ! $files->isDirectory($this->getPath())) {
            $this->components->error('Theme "' . $this->getTheme() . '" is not exists.');

            return self::FAILURE;
        }

        $result = $themeService->publishAssets($this->option('name'));

        if ($result['error']) {
            $this->components->error($result['message']);

            return self::FAILURE;
        }

        $this->components->info($result['message']);

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addOption('name', null, InputOption::VALUE_REQUIRED, 'The theme name that you want to remove assets');
        $this->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path to theme directory');
    }
}
