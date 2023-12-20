<?php

namespace Botble\Base\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\Process;

#[AsCommand('cms:db:export', 'Export database to SQL file.')]
class ExportDatabaseCommand extends Command
{
    public function handle(): int
    {
        $config = DB::connection('mysql')->getConfig();

        if (! $config) {
            return self::FAILURE;
        }

        $sqlPath = base_path('database.sql');

        $sql = 'mysqldump --user="' . $config['username'] . '" --password="' . $config['password'] . '"';

        $sql .= ' --host=' . $config['host'] . ' --port=' . $config['port'] . ' ' . $config['database'] . ' > ' . $sqlPath;

        Process::fromShellCommandline($sql)->mustRun();

        $this->components->info('Exported database to SQL file successfully!');

        return self::SUCCESS;
    }
}
