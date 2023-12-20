<?php

namespace Botble\Base\Commands;

use Botble\Base\Services\CleanDatabaseService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:system:cleanup', 'All the preloaded data will be deleted from the database except few mandatory record that is essential for running the software properly.')]
class CleanupSystemCommand extends Command
{
    use ConfirmableTrait;

    public function handle(CleanDatabaseService $cleanDatabaseService): int
    {
        try {
            if ($this->confirmToProceed('Are you sure you want to cleanup your database?')) {
                $this->components->task('Cleaning database', fn () => $cleanDatabaseService->execute());
            }

            $this->components->info('Cleaned database successfully!');
        } catch (Exception $exception) {
            $this->components->error($exception->getMessage());
        }

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addOption('force', 'f', null, 'Cleanup database without confirmation');
    }
}
