<?php

namespace Botble\Translation\Console;

use Botble\Translation\Manager;
use Botble\Translation\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:translations:remove-unused-translations', 'Remove unused translations')]
class RemoveUnusedTranslationsCommand extends Command
{
    public function handle(Manager $manager): int
    {
        $this->components->info('Remove unused translations in resource/lang...');

        foreach (File::directories(lang_path('vendor/packages')) as $package) {
            if (! File::isDirectory(package_path(File::basename($package)))) {
                File::deleteDirectory($package);
            }
        }

        foreach (File::directories(lang_path('vendor/plugins')) as $plugin) {
            if (! File::isDirectory(plugin_path(File::basename($plugin)))) {
                File::deleteDirectory($plugin);
            }
        }

        $manager->removeUnusedThemeTranslations();

        $this->components->info('Importing...');
        $manager->importTranslations();

        $groups = Translation::query()->groupBy('group')->pluck('group');

        $counter = 0;
        foreach ($groups as $group) {
            $keys = Translation::query()->where('group', $group)
                ->where('locale', 'en')
                ->pluck('key');

            $counter += Translation::query()->where('locale', '!=', 'en')
                ->where('group', $group)
                ->whereNotIn('key', $keys)
                ->delete();
        }

        $manager->exportAllTranslations();

        $this->components->info('Exporting...');
        $this->components->info(sprintf('Done! Deleted %s items!', number_format($counter)));

        return self::SUCCESS;
    }
}
