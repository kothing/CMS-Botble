<?php

namespace Botble\Translation\Console;

use Botble\Translation\Manager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('cms:translations:import', 'Import translations from the PHP sources')]
class ImportCommand extends Command
{
    public function handle(Manager $manager): int
    {
        $this->components->info('Importing...');
        $replace = $this->option('replace');
        $counter = $manager->importTranslations($replace);
        $this->components->info(sprintf('Done importing, processed %s items!', number_format($counter)));

        return self::SUCCESS;
    }

    protected function getOptions(): array
    {
        return [
            ['replace', 'R', InputOption::VALUE_NONE, 'Replace existing keys'],
        ];
    }
}
