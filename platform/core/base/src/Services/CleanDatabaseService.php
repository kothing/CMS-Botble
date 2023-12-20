<?php

namespace Botble\Base\Services;

use Botble\Setting\Facades\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CleanDatabaseService
{
    public function getIgnoreTables(): array
    {
        return [
            'migrations',
            'pages',
            'users',
            'activations',
            'settings',
            'translations',
            'widgets',
            'menus',
            'menu_nodes',
        ];
    }

    public function execute(array $except = []): bool
    {
        $except = array_merge($except, $this->getIgnoreTables());

        try {
            $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            $tables = array_diff($tables, $except);
        } catch (Throwable) {
            $tables = [];
        }

        if (empty($tables)) {
            return false;
        }

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        Setting::delete(except: [
            'theme',
            'activated_plugins',
            'licensed_to',
            'media_random_hash',
        ]);

        File::cleanDirectory(Storage::disk()->path(''));

        return true;
    }
}
