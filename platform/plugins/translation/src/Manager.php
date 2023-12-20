<?php

namespace Botble\Translation;

use ArrayAccess;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\PclZip as Zip;
use Botble\Base\Supports\ServiceProvider;
use Botble\Theme\Facades\Theme;
use Botble\Translation\Models\Translation;
use Exception;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\VarExporter\VarExporter;
use Throwable;
use ZipArchive;

class Manager
{
    protected array|ArrayAccess $config;

    public function __construct(protected Application $app, protected Filesystem $files)
    {
        $this->config = $app['config']['plugins.translation.general'];
    }

    public function importTranslations(bool $replace = false): int
    {
        try {
            $this->publishLocales();
        } catch (Exception $exception) {
            info($exception->getMessage());
        }

        $counter = 0;

        foreach ($this->files->directories(lang_path()) as $langPath) {
            $locale = basename($langPath);
            foreach ($this->files->allFiles($langPath) as $file) {
                $info = pathinfo($file);
                $group = $info['filename'];
                if (in_array($group, $this->config['exclude_groups'])) {
                    continue;
                }
                $subLangPath = str_replace($langPath . DIRECTORY_SEPARATOR, '', $info['dirname']);
                $subLangPath = str_replace(DIRECTORY_SEPARATOR, '/', $subLangPath);
                $langDirectory = $group;
                if ($subLangPath != $langPath) {
                    $langDirectory = $subLangPath . '/' . $group;
                    $group = substr($subLangPath, 0, -3) . '/' . $group;
                }

                $translations = Lang::getLoader()->load($locale, $langDirectory);
                if ($translations && is_array($translations)) {
                    foreach (Arr::dot($translations) as $key => $value) {
                        $importedTranslation = $this->importTranslation(
                            $key,
                            $value,
                            ($locale != 'vendor' ? $locale : substr($subLangPath, -2)),
                            $group,
                            $replace
                        );
                        $counter += $importedTranslation ? 1 : 0;
                    }
                }
            }
        }

        return $counter;
    }

    public function publishLocales(): void
    {
        $paths = ServiceProvider::pathsToPublish(null, 'cms-lang');

        foreach ($paths as $from => $to) {
            $this->files->ensureDirectoryExists(dirname($to));
            $this->files->copyDirectory($from, $to);
        }

        if (! File::isDirectory(lang_path('en'))) {
            $this->downloadRemoteLocale('en');
        }
    }

    public function importTranslation(
        string $key,
        string|null|array $value,
        string|null $locale,
        string|null $group,
        bool $replace = false
    ): bool {
        // process only string values
        if (is_array($value)) {
            return false;
        }

        $value = (string)$value;
        $translation = Translation::query()->firstOrNew([
            'locale' => $locale,
            'group' => $group,
            'key' => $key,
        ]);

        // Check if the database is different from files
        $newStatus = $translation->value === $value ? Translation::STATUS_SAVED : Translation::STATUS_CHANGED;
        if ($newStatus !== (int)$translation->status) {
            $translation->status = $newStatus;
        }

        // Only replace when empty, or explicitly told so
        if ($replace || ! $translation->value) {
            $translation->value = $value;
        }

        $translation->save();

        return true;
    }

    public function exportTranslations(string|null $group = null): void
    {
        if (! empty($group)) {
            if (! in_array($group, $this->config['exclude_groups'])) {
                if ($group == '*') {
                    $this->exportAllTranslations();

                    return;
                }

                $tree = $this->makeTree(
                    Translation::ofTranslatedGroup($group)->orderByGroupKeys(
                        Arr::get(
                            $this->config,
                            'sort_keys',
                            false
                        )
                    )->get()
                );

                foreach ($tree as $locale => $groups) {
                    if (isset($groups[$group])) {
                        $translations = $groups[$group];

                        if (
                            $locale != 'en' &&
                            isset($tree['en'][$group]) &&
                            is_array($tree['en'][$group]) &&
                            count($tree['en'][$group]) !== count($translations)
                        ) {
                            $translations = array_merge($tree['en'][$group], $translations);
                        }

                        $file = $locale . '/' . $group;

                        if (! $this->files->isDirectory(lang_path($locale))) {
                            $this->files->makeDirectory(lang_path($locale), 755, true);
                        }

                        $groups = explode('/', $group);
                        if (count($groups) > 1) {
                            $folderName = Arr::last($groups);
                            Arr::forget($groups, count($groups) - 1);

                            $dir = 'vendor/' . implode('/', $groups) . '/' . $locale;
                            if (! $this->files->isDirectory(lang_path($dir))) {
                                $this->files->makeDirectory(lang_path($dir), 755, true);
                            }

                            $file = $dir . '/' . $folderName;
                        }
                        $path = lang_path($file . '.php');
                        $output = "<?php\n\nreturn " . VarExporter::export($translations) . ";\n";
                        $this->files->put($path, $output);
                    }
                }

                Translation::ofTranslatedGroup($group)->update(['status' => Translation::STATUS_SAVED]);
            }
        }
    }

    public function exportAllTranslations(): bool
    {
        $groups = Translation::query()->whereNotNull('value')->selectDistinctGroup()->get('group');

        foreach ($groups as $group) {
            $this->exportTranslations($group->group);
        }

        return true;
    }

    protected function makeTree(array|Collection $translations): array
    {
        $array = [];
        foreach ($translations as $translation) {
            Arr::set($array, "$translation->locale.$translation->group.$translation->key", $translation->value);
        }

        return $array;
    }

    public function cleanTranslations(): void
    {
        Translation::query()->whereNull('value')->delete();
    }

    public function truncateTranslations(): void
    {
        Translation::query()->truncate();
    }

    public function getConfig(string|null $key = null): string|array|null
    {
        if ($key == null) {
            return $this->config;
        }

        return $this->config[$key];
    }

    public function removeUnusedThemeTranslations(): bool
    {
        if (! defined('THEME_MODULE_SCREEN_NAME')) {
            File::deleteDirectory(lang_path('vendor/themes'));

            return false;
        }

        $existingThemes = BaseHelper::scanFolder(theme_path());

        foreach (BaseHelper::scanFolder(lang_path('vendor/themes')) as $theme) {
            if (! in_array($theme, $existingThemes)) {
                File::deleteDirectory(lang_path("vendor/themes/$theme"));
            }
        }

        $theme = Theme::getThemeName();

        foreach ($this->files->allFiles(lang_path("vendor/themes/$theme")) as $file) {
            if ($this->files->isFile($file) && $file->getExtension() === 'json') {
                $locale = $file->getFilenameWithoutExtension();

                if ($locale == 'en') {
                    continue;
                }

                $translations = BaseHelper::getFileData($file->getRealPath());

                $defaultEnglishFile = theme_path("$theme/lang/en.json");

                if ($defaultEnglishFile) {
                    $enTranslations = BaseHelper::getFileData($defaultEnglishFile);
                    $translations = array_merge($enTranslations, $translations);

                    $enTranslationKeys = array_keys($enTranslations);

                    foreach ($translations as $key => $translation) {
                        if (! in_array($key, $enTranslationKeys)) {
                            Arr::forget($translations, $key);
                        }
                    }
                }

                ksort($translations);

                $this->files->put(
                    $file->getRealPath(),
                    json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                );
            }
        }

        return true;
    }

    public function getRemoteAvailableLocales(): array
    {
        try {
            $info = Http::withoutVerifying()
                ->asJson()
                ->acceptJson()
                ->get('https://api.github.com/repos/botble/translations/git/trees/master');

            if (! $info->ok()) {
                return ['ar', 'es', 'vi'];
            }

            $info = $info->json();

            $availableLocales = [];

            foreach ($info['tree'] as $tree) {
                if (in_array($tree['path'], ['.gitignore', 'README.md'])) {
                    continue;
                }

                $availableLocales[] = $tree['path'];
            }
        } catch (Throwable) {
            $availableLocales = ['ar', 'es', 'vi'];
        }

        return $availableLocales;
    }

    public function downloadRemoteLocale(string $locale): array
    {
        $repository = 'https://github.com/botble/translations';

        $destination = storage_path('app/translation-files.zip');

        $availableLocales = $this->getRemoteAvailableLocales();

        if (! in_array($locale, $availableLocales)) {
            return [
                'error' => true,
                'message' => sprintf('This locale is not available on %s', $repository),
            ];
        }

        try {
            $response = Http::withoutVerifying()
                ->sink(Utils::tryFopen($destination, 'w'))
                ->get($repository . '/archive/refs/heads/master.zip');

            if (! $response->ok()) {
                return [
                    'error' => true,
                    'message' => $response->reason(),
                ];
            }
        } catch (Throwable $exception) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
            ];
        }

        if (class_exists('ZipArchive', false)) {
            $zip = new ZipArchive();
            $res = $zip->open($destination);
            if ($res === true) {
                $zip->extractTo(storage_path('app'));
                $zip->close();
            } else {
                return [
                    'error' => true,
                    'message' => 'Extract translation files failed!',
                ];
            }
        } else {
            $archive = new Zip($destination);
            $archive->extract(PCLZIP_OPT_PATH, storage_path('app'));
        }

        if (File::exists($destination)) {
            unlink($destination);
        }

        $localePath = storage_path('app/translations-master/' . $locale);

        File::copyDirectory($localePath . '/' . $locale, lang_path($locale));
        File::copyDirectory($localePath . '/vendor/core', lang_path('vendor/core'));
        File::copyDirectory($localePath . '/vendor/packages', lang_path('vendor/packages'));
        File::copyDirectory($localePath . '/vendor/plugins', lang_path('vendor/plugins'));

        $theme = Theme::getThemeName();

        File::ensureDirectoryExists(lang_path("vendor/themes/$theme"));

        if (File::exists($themeJsonPath = "$localePath/vendor/themes/$theme/$locale.json")) {
            File::copy($themeJsonPath, lang_path("vendor/themes/$theme/$locale.json"));
        } else {
            $jsonFile = $localePath . '/' . $locale . '.json';

            File::copy($jsonFile, lang_path("vendor/themes/$theme/$locale.json"));

            $this->removeUnusedThemeTranslations();
        }

        File::deleteDirectory(storage_path('app/translations-master'));

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

        return [
            'error' => false,
            'message' => 'Downloaded translation files!',
        ];
    }

    public function getThemeTranslations(string $locale): array
    {
        $translations = BaseHelper::getFileData($this->getThemeTranslationPath($locale));

        ksort($translations);

        if ($locale !== 'en' && $defaultEnglishFile = theme_path(Theme::getThemeName() . '/lang/en.json')) {
            $enTranslations = BaseHelper::getFileData($defaultEnglishFile);
            $translations = array_merge($enTranslations, $translations);

            $enTranslationKeys = array_keys($enTranslations);

            foreach ($translations as $key => $translation) {
                if (! in_array($key, $enTranslationKeys)) {
                    Arr::forget($translations, $key);
                }
            }
        }

        return array_combine(array_map('trim', array_keys($translations)), $translations);
    }

    public function getThemeTranslationPath(string $locale): string
    {
        $theme = Theme::getThemeName();

        $localeFilePath = $defaultLocaleFilePath = lang_path("vendor/themes/$theme/$locale.json");

        if (! File::exists($localeFilePath)) {
            $localeFilePath = lang_path("$locale.json");
        }

        if (! File::exists($localeFilePath)) {
            $localeFilePath = $defaultLocaleFilePath;

            File::ensureDirectoryExists(dirname($localeFilePath));

            $themeLangPath = theme_path("$theme/lang/$locale.json");

            if (! File::exists($themeLangPath)) {
                $themeLangPath = theme_path("$theme/lang/en.json");
            }

            File::copy($themeLangPath, $localeFilePath);
        }

        return $localeFilePath;
    }

    public function saveThemeTranslations(string $locale, array $translations): bool
    {
        ksort($translations);

        return BaseHelper::saveFileData($this->getThemeTranslationPath($locale), $translations);
    }
}
