<?php

namespace Botble\Backup\Commands;

use Botble\Backup\Supports\Backup;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('cms:backup:create', 'Create a backup')]
class BackupCreateCommand extends Command
{
    public function handle(Backup $backupService): int
    {
        try {
            $this->components->info('Generating backup...');

            $data = $backupService->createBackupFolder($this->argument('name'), $this->option('description'));
            $backupService->backupDb();
            $backupService->backupFolder(Storage::path(''));
            do_action(BACKUP_ACTION_AFTER_BACKUP, BACKUP_MODULE_SCREEN_NAME, request());

            $this->components->info('Done! The backup folder is located in ' . $backupService->getBackupPath($data['key']) . '!');
        } catch (Exception $exception) {
            $this->components->error($exception->getMessage());
        }

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of backup');
        $this->addOption('description', null, InputOption::VALUE_REQUIRED, 'The description');
    }
}
