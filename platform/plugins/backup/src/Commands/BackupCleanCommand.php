<?php

namespace Botble\Backup\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:backup:clean', 'Remove all backup')]
class BackupCleanCommand extends Command
{
    use ConfirmableTrait;

    public function handle(): int
    {
        if (! $this->confirmToProceed('Clean all backup?', true)) {
            return self::FAILURE;
        }

        File::deleteDirectory(storage_path('app/backup'));

        $this->components->info('Remove all backup successfully!');

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addOption('force', 'f', null, 'Remove all backup without confirmation');
    }
}
