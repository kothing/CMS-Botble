<?php

namespace Botble\Translation\Console;

use Botble\Theme\Facades\Theme;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:translations:remove-locale', 'Remove a locale')]
class RemoveLocaleCommand extends Command
{
    use ConfirmableTrait;

    public function handle(): int
    {
        if (! $this->confirmToProceed('Are you sure you want to permanently delete?', true)) {
            return self::FAILURE;
        }

        if (! preg_match('/^[a-z0-9\-]+$/i', $this->argument('locale'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $locale = $this->argument('locale');

        $defaultLocale = lang_path($locale);
        if ($this->laravel['files']->exists($defaultLocale)) {
            $this->laravel['files']->deleteDirectory($defaultLocale);
            $this->components->info('Deleted: ' . $defaultLocale);
        }

        $this->removeLocaleInPath(lang_path('vendor/core'));
        $this->removeLocaleInPath(lang_path('vendor/packages'));
        $this->removeLocaleInPath(lang_path('vendor/plugins'));

        $theme = Theme::getThemeName();

        $jsonFile = lang_path("vendor/themes/$theme/$locale.json");

        if ($this->laravel['files']->exists($jsonFile)) {
            $this->laravel['files']->delete($jsonFile);
        }

        $this->components->info('Removed locale "' . $this->argument('locale') . '" successfully!');

        return self::SUCCESS;
    }

    protected function removeLocaleInPath(string $path): int
    {
        if (! $this->laravel['files']->isDirectory($path)) {
            return self::SUCCESS;
        }

        $folders = $this->laravel['files']->directories($path);

        foreach ($folders as $module) {
            foreach ($this->laravel['files']->directories($module) as $locale) {
                if ($this->laravel['files']->name($locale) == $this->argument('locale')) {
                    $this->laravel['files']->deleteDirectory($locale);
                    $this->components->info('Deleted: ' . $locale);
                }
            }
        }

        return count($folders);
    }

    protected function configure(): void
    {
        $this->addArgument('locale', InputArgument::REQUIRED, 'The locale name that you want to remove');

        $this->addOption('force', 'f', null, 'Remove locale` backup without confirmation');
    }
}
