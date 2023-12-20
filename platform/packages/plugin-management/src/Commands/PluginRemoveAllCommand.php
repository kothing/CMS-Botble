<?php

namespace Botble\PluginManagement\Commands;

use Botble\Base\Facades\BaseHelper;
use Botble\PluginManagement\Services\PluginService;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:plugin:remove:all', 'Remove all plugins in /plugins directory')]
class PluginRemoveAllCommand extends Command
{
    use ConfirmableTrait;

    public function handle(PluginService $pluginService): int
    {
        if (! $this->confirmToProceed('Are you sure you want to remove ALL plugins?', true)) {
            return self::FAILURE;
        }

        foreach (BaseHelper::scanFolder(plugin_path()) as $plugin) {
            $pluginService->remove($plugin);
        }

        $this->components->info('Removed successfully!');

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addOption('force', 'f', null, 'Force to remove ALL plugins without confirmation');
    }
}
