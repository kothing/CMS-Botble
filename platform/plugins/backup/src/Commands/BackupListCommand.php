<?php

namespace Botble\Backup\Commands;

use Botble\Backup\Supports\Backup;
use Botble\Base\Facades\BaseHelper;
use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:backup:list', 'List all backups')]
class BackupListCommand extends Command
{
    public function handle(Backup $backupService): int
    {
        try {
            $backups = BaseHelper::getFileData($backupService->getBackupPath('backup.json'));

            foreach ($backups as $key => &$item) {
                $item['key'] = $key;
            }

            $header = [
                'Name',
                'Description',
                'Date',
                'Folder',
            ];

            $this->table($header, $backups);
        } catch (Exception $exception) {
            $this->components->error($exception->getMessage());
        }

        return self::SUCCESS;
    }
}
