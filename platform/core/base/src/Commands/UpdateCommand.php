<?php

namespace Botble\Base\Commands;

use Botble\Base\Events\UpdatedEvent;
use Botble\Base\Events\UpdatingEvent;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Core;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Process\Process;
use Throwable;

#[AsCommand('cms:update', 'Update system to latest version')]
class UpdateCommand extends Command
{
    use ConfirmableTrait;

    public function __construct(protected Core $core, protected Composer $composer)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! config('core.base.general.enable_system_updater')) {
            $this->components->error('Please enable system updater first.');

            return self::FAILURE;
        }

        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        $this->components->info('Checking for the latest version...');

        $latestUpdate = $this->core->getLatestVersion();

        if (! $latestUpdate) {
            $this->components->error('Your license is invalid. Please activate your license first.');

            return self::FAILURE;
        }

        if (version_compare($latestUpdate->version, $this->core->version(), '<=')) {
            if ($this->components->confirm(
                sprintf('Your current system version <info>%s</info> is the latest version. Do you want to reinstall this update?', $latestUpdate->version)
            )) {
                return $this->performUpdate($latestUpdate->updateId, $latestUpdate->version);
            }

            $this->components->info('Your system is up to date.');

            return self::SUCCESS;
        }

        $this->components->info(
            sprintf('A new version (<comment>%s</comment> / released on <comment>%s</comment>) is available to update!', $latestUpdate->version, $latestUpdate->releasedDate->format('Y-m-d'))
        );

        $this->components->warn('Notice:');

        array_map(fn ($line) => $this->line($line), [
            'Please backup your database and script files before upgrading',
            'You need to activate your license before doing upgrade.',
            'If you don\'t need this 1-click update, you can disable it in <fg=yellow>.env</>? by adding <fg=yellow>CMS_ENABLE_SYSTEM_UPDATER=false</>',
            'It will override all files in <fg=yellow>./platform/core</>, <fg=yellow>./platform/packages</>, all plugins developed by us in <fg=yellow>./platform/plugins</> and theme developed by us in <fg=yellow>./platform/themes</>.',
        ]);

        if ($this->components->confirm('Do you really wish to run this command?', true)) {
            return $this->performUpdate($latestUpdate->updateId, $latestUpdate->version);
        }

        return self::SUCCESS;
    }

    protected function performUpdate(string $updateId, string $version): int
    {
        event(new UpdatingEvent());

        $progressBar = new ProgressBar($this->output, 6);
        $progressBar->setMessage('Verifying license...');
        $progressBar->setFormat("%current%/%max% %bar%\n%message%");
        $progressBar->setBarCharacter('<info>█</info>');
        $progressBar->setEmptyBarCharacter('░');
        $progressBar->setProgressCharacter('<info>█</info>');
        $progressBar->setBarWidth(50);
        $progressBar->start();

        try {
            if (! $this->core->verifyLicense(true)) {
                $this->errorWithNewLines('Your license is invalid. Please activate your license first.');

                return self::FAILURE;
            }

            $progressBar->setMessage('Downloading the latest update...');
            $progressBar->advance();

            if (! $this->core->downloadUpdate($updateId, $version)) {
                $this->errorWithNewLines('Could not download updated file. Please check your license or your internet network.');

                return self::FAILURE;
            }

            $progressBar->setMessage('Updating files and database...');
            $progressBar->advance();

            if (! $this->core->updateFilesAndDatabase($version)) {
                $this->errorWithNewLines('Could not update files & database.');

                return self::FAILURE;
            }

            $progressBar->setMessage('Publishing all assets...');
            $progressBar->advance();
            $this->core->publishUpdateAssets();

            $progressBar->setMessage('Cleaning up the system...');
            $progressBar->advance();
            $this->core->cleanCaches();
        } catch (Throwable $exception) {
            $this->errorWithNewLines($exception->getMessage());
            $this->core->logError($exception);

            return self::FAILURE;
        }

        $progressBar->setMessage('Done.');
        $progressBar->advance();
        $progressBar->finish();

        event(new UpdatedEvent());

        $this->infoWithNewLines('Your system has been updated successfully.');

        if ($this->confirm('Do you want run <comment>composer</comment> command?', true)) {
            $process = new Process(array_merge($this->composer->findComposer(), [
                $this->components->choice('Run <comment>composer install</comment> or <comment>composer update</comment>?', [
                    'install',
                    'update',
                ], 'install'),
            ]));
            $process->start();

            $process->wait(function ($type, $buffer) {
                $this->line($buffer);
            });
        }

        return self::SUCCESS;
    }

    protected function errorWithNewLines(string $message): void
    {
        $this->newLine();
        $this->newLine();
        $this->components->error($message);
    }

    protected function infoWithNewLines(string $message): void
    {
        $this->newLine();
        $this->newLine();
        $this->components->info($message);
    }
}
