<?php

namespace Botble\Translation\Console;

use Botble\Translation\Manager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:translations:export', 'Export translations to PHP files')]
class ExportCommand extends Command
{
    public function handle(Manager $manager): int
    {
        $group = $this->argument('group');

        if (empty($group)) {
            $this->components->warn('You must either specify a group argument');

            return self::FAILURE;
        }

        $manager->exportTranslations($group);

        $this->components->info('Done writing language files for ' . ($group == '*' ? 'ALL groups' : $group . ' group'));

        return self::SUCCESS;
    }

    protected function getArguments(): array
    {
        return [
            ['group', InputArgument::OPTIONAL, 'The group to export (`*` for all).'],
        ];
    }
}
