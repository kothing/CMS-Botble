<?php

namespace Botble\Base\Supports;

use Botble\Base\Events\FinishedSeederEvent;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Models\MediaFile;
use Botble\Media\Models\MediaFolder;
use Botble\PluginManagement\Services\PluginService;
use Botble\Setting\Facades\Setting;
use Botble\Slug\Models\Slug;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Facades\ThemeOption;
use Exception;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Mimey\MimeTypes;
use Symfony\Component\Process\Process;
use Throwable;

class BaseSeeder extends Seeder
{
    protected Generator $faker;

    public function uploadFiles(string $folder, string|null $basePath = null): array
    {
        $storage = Storage::disk('public');

        if ($storage->exists($folder)) {
            $storage->deleteDirectory($folder);
        }

        MediaFile::query()->where('url', 'LIKE', $folder . '/%')->forceDelete();
        MediaFolder::query()->where('name', $folder)->forceDelete();

        $mimeType = new MimeTypes();

        $files = [];

        $folderPath = ($basePath ?: database_path('seeders/files/' . $folder));

        if (! File::isDirectory($folderPath)) {
            return [];
        }

        foreach (File::allFiles($folderPath) as $file) {
            $type = $mimeType->getMimeType(File::extension($file));
            $files[] = RvMedia::uploadFromPath($file, 0, $folder, $type);
        }

        return $files;
    }

    public function activateAllPlugins(): array
    {
        try {
            $plugins = array_values(BaseHelper::scanFolder(plugin_path()));

            $pluginService = app(PluginService::class);

            foreach ($plugins as $plugin) {
                $pluginService->activate($plugin);
            }

            return $plugins;
        } catch (Exception) {
            return [];
        }
    }

    public function prepareRun(): void
    {
        if (! class_exists(\Faker\Factory::class)) {
            $this->command->warn('It requires <info>fakerphp/faker</info> to run seeder. Need to run <info>composer install</info> to install it first.');

            try {
                $composer = new Composer($this->command->getLaravel()['files']);

                $process = new Process(array_merge($composer->findComposer(), ['install']));

                $process->start();

                $process->wait(function ($type, $buffer) {
                    $this->command->line($buffer);
                });

                $this->command->warn('Please re-run <info>php artisan db:seed</info> command.');
            } catch (Throwable) {
            }

            exit(1);
        }

        Setting::newQuery()->truncate();

        Setting::forgetAll();

        $this->activateAllPlugins();

        Setting::set([
            'media_random_hash' => md5((string)time()),
            'api_enabled' => 0,
            'show_admin_bar' => 1,
            'theme' => Theme::getThemeName(),
        ])->save();

        Slug::query()->truncate();
        MetaBoxModel::query()->truncate();
    }

    protected function random(int $from, int $to, array $exceptions = []): int
    {
        sort($exceptions); // lets us use break; in the foreach reliably
        $number = rand($from, $to - count($exceptions)); // or mt_rand()

        foreach ($exceptions as $exception) {
            if ($number >= $exception) {
                $number++; // make up for the gap
            } else { /*if ($number < $exception)*/
                break;
            }
        }

        return $number;
    }

    protected function finished(): void
    {
        event(new FinishedSeederEvent());
    }

    protected function prepareThemeOptions(array $options, string $locale = null, string $defaultLocale = null): array
    {
        return collect($options)
            ->mapWithKeys(function (string $value, string $key) use ($locale, $defaultLocale) {
                return [ThemeOption::getOptionKey($key, $locale != $defaultLocale ? $locale : null) => $value];
            })
            ->all();
    }

    protected function fake(): Generator
    {
        if (isset($this->faker)) {
            return $this->faker;
        }

        $this->faker = fake();

        return $this->faker;
    }
}
