<?php

namespace Botble\Theme\Commands;

use Botble\Language\Facades\Language;
use Botble\Setting\Facades\Setting;
use Botble\Theme\Facades\ThemeOption;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:theme:options:check', 'Check difference theme options between database and option definitions')]
class ThemeOptionCheckMissingCommand extends Command
{
    public function handle(): int
    {
        $isReverse = $this->option('reverse');

        $fields = array_map(function ($name) {
            return ThemeOption::getOptionKey($name);
        }, array_keys(Arr::get(ThemeOption::getFields(), 'theme')));

        $existsOptionsQuery = Setting::newQuery();
        $existsOptionsQuery->where('key', 'LIKE', ThemeOption::getOptionKey('%'));

        if (is_plugin_active('language')) {
            foreach (Language::getSupportedLanguagesKeys() as $language) {
                $existsOptionsQuery->where('key', 'NOT LIKE', ThemeOption::getOptionKey('%', $language));
            }
        }

        $existsOptions = $existsOptionsQuery->pluck('key')->all();
        $missingKeys = $isReverse
            ? $this->missingKeys($existsOptions, $fields)
            : $this->missingKeys($fields, $existsOptions);

        if ($missingKeys->isEmpty()) {
            $this->components->info('No missing option found!');

            return self::SUCCESS;
        }

        $missingKeysCount = $missingKeys->count();
        $pluralKeyWord = Str::plural('key', $missingKeysCount);
        $this->line(
            $isReverse
                ? 'We found <info>' . $missingKeysCount . '</info> ' . $pluralKeyWord . ' are not exists in settings table (database).'
                : 'We found <info>' . $missingKeysCount . '</info> ' . $pluralKeyWord . ' are not defined in theme options.'
        );
        $this->table(['#', 'Key'], $missingKeys->toArray());

        return self::SUCCESS;
    }

    protected function missingKeys(array $items, array $origin): Collection
    {
        return collect($items)->filter(function ($item) use ($origin) {
            return ! in_array($item, $origin);
        })->values()->map(function ($item, $key) {
            return [$key, $item];
        });
    }

    protected function configure(): void
    {
        $this->addOption('reverse', 'R', null, 'Reverse results');
    }
}
