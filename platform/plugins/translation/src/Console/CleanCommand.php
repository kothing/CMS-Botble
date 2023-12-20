<?php

namespace Botble\Translation\Console;

use Botble\Translation\Manager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:translations:clean', 'Clean empty translations')]
class CleanCommand extends Command
{
    public function handle(Manager $manager): int
    {
        $manager->cleanTranslations();

        $this->components->info('Done cleaning translations');

        return self::SUCCESS;
    }
}
