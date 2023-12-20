<?php

namespace Botble\Language\Commands;

use Botble\Language\Facades\Language;
use Botble\Language\Models\LanguageMeta;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:language:sync', 'Set default language for old objects')]
class SyncOldDataCommand extends Command
{
    public function handle(): int
    {
        if (! Language::getDefaultLanguage()) {
            $this->components->error('No languages in the system, please add a language!');

            return self::FAILURE;
        }

        $class = $this->argument('class');
        $table = (new $class())->getTable();

        if (! Schema::hasTable($table)) {
            $this->components->error(sprintf('Table [%s] is not existed!', $table));

            return self::FAILURE;
        }

        if (! Schema::hasColumn($table, 'id')) {
            $this->components->error(sprintf('Table [%s] does not have ID column!', $table));

            return self::FAILURE;
        }

        $ids = LanguageMeta::query()->where('reference_type', $class)
            ->pluck('reference_id')
            ->all();

        $referenceIds = DB::table($table)
            ->whereNotIn('id', $ids)
            ->pluck('id')
            ->all();

        $data = [];
        foreach ($referenceIds as $referenceId) {
            $data[] = [
                'reference_id' => $referenceId,
                'reference_type' => $class,
                'lang_meta_code' => Language::getDefaultLocaleCode(),
                'lang_meta_origin' => md5($referenceId . $class . time()),
            ];
        }

        LanguageMeta::query()->insert($data);

        $this->components->info('Processed ' . count($data) . ' item(s)!');

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The model class name');
    }
}
