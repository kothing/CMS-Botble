<?php

namespace Botble\Backup\Commands;

use Botble\Backup\Supports\Backup;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:backup:remove', 'Remove a backup')]
class BackupRemoveCommand extends Command
{
    use ConfirmableTrait;

    public function handle(Backup $backupService): int
    {
        try {
            $backup = $this->argument('backup');

            if (! File::isDirectory($backupService->getBackupPath($backup))) {
                $this->components->error('Cannot found backup folder!');

                return self::FAILURE;
            }

            if (! $this->confirmToProceed('Are you sure you want to permanently delete?', true)) {
                return self::FAILURE;
            }

            $backupService->deleteFolderBackup($backupService->getBackupPath($backup));

            $this->components->info('Remove a backup successfully!');
        } catch (Exception $exception) {
            $this->components->error($exception->getMessage());
        }

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('backup', InputArgument::REQUIRED, 'The backup date');
        $this->addOption('force', 'f', null, 'Remove backup without confirmation');
    }
}
