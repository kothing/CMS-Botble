<?php

namespace Botble\PluginManagement\Commands;

use Botble\PluginManagement\Commands\Concern\HasPluginNameValidation;
use Botble\PluginManagement\Services\PluginService;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:plugin:remove', 'Remove a plugin in the /platform/plugins directory.')]
class PluginRemoveCommand extends Command
{
    use ConfirmableTrait;
    use HasPluginNameValidation;

    public function handle(PluginService $pluginService): int
    {
        if (! $this->confirmToProceed('Are you sure you want to permanently delete?', true)) {
            return self::FAILURE;
        }

        $this->validatePluginName($this->argument('name'));

        $plugin = strtolower($this->argument('name'));
        $result = $pluginService->remove($plugin);

        if ($result['error']) {
            $this->components->error($result['message']);

            return self::FAILURE;
        }

        $this->components->info($result['message']);

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The plugin that you want to remove');
        $this->addOption('force', 'f', null, 'Force to remove plugin without confirmation');
    }
}
