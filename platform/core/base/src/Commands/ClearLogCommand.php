<?php

namespace Botble\Base\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:log:clear', 'Clear log files')]
class ClearLogCommand extends Command
{
    public function handle(Filesystem $filesystem): int
    {
        $logPath = storage_path('logs');

        if (! $filesystem->isDirectory($logPath)) {
            return self::FAILURE;
        }

        $this->components->task('Clearing log files', function () use ($filesystem, $logPath) {
            foreach ($filesystem->allFiles($logPath) as $file) {
                $this->components->info(sprintf('Deleting [%s]', $file->getPathname()));
                $filesystem->delete($file->getPathname());
            }
        });

        $this->components->info('Clear log files successfully!');

        return self::SUCCESS;
    }
}
