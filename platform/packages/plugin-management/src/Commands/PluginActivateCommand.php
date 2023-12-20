<?php

namespace Botble\PluginManagement\Commands;

use Botble\PluginManagement\Commands\Concern\HasPluginNameValidation;
use Botble\PluginManagement\Services\PluginService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:plugin:activate', 'Activate a plugin in /plugins directory')]
class PluginActivateCommand extends Command
{
    use HasPluginNameValidation;

    public function handle(PluginService $pluginService): int
    {
        $this->validatePluginName($this->argument('name'));

        $plugin = strtolower($this->argument('name'));

        $result = $pluginService->activate($plugin);

        if ($result['error']) {
            $this->components->error($result['message']);

            return self::FAILURE;
        }

        $this->components->info($result['message']);

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The plugin that you want to activate');
    }
}
