<?php

namespace Botble\Translation\Console;

use Botble\Translation\Manager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:translations:download-locale', 'Download translation files from https://github.com/botble/translations')]
class DownloadLocaleCommand extends Command
{
    public function handle(Manager $manager): int
    {
        $this->components->info('Downloading...');

        $result = $manager->downloadRemoteLocale($this->argument('locale'));

        if ($result['error']) {
            $this->components->error($result['message']);

            return self::FAILURE;
        }

        $this->components->info($result['message']);

        return self::SUCCESS;
    }

    protected function getArguments(): array
    {
        return [
            ['locale', InputArgument::REQUIRED, 'The locale that you want to download'],
        ];
    }
}
