<?php

namespace Botble\Menu\Commands;

use Botble\Menu\Facades\Menu;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:menu:clear-cache', 'Clear cache menu URLs')]
class ClearMenuCacheCommand extends Command
{
    public function handle(): int
    {
        Menu::clearCacheMenuItems();

        $this->components->info('Menu cache URLs cleared!');

        return self::SUCCESS;
    }
}
