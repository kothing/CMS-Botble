<?php

namespace Botble\PluginManagement\Commands;

use Botble\PluginManagement\PluginManifest;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:plugin:discover', 'Rebuild the cached plugin manifest')]
class PluginDiscoverCommand extends Command
{
    public function handle(PluginManifest $manifest): int
    {
        $this->components->info('Discovering plugins');

        $this->laravel['files']->delete($manifest->getManifestFilePath());

        $manifest->generateManifest();

        collect(get_active_plugins())
            ->each(fn ($description) => $this->components->task($description))
            ->whenNotEmpty(fn () => $this->newLine());

        return self::SUCCESS;
    }
}
