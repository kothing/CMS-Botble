<?php

namespace Botble\PluginManagement\Commands;

use Botble\Base\Facades\BaseHelper;
use Botble\PluginManagement\Services\PluginService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand('cms:plugin:list', 'Show all plugins information')]
class PluginListCommand extends Command
{
    public function __construct(protected PluginService $pluginService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $header = [
            'Name',
            'Alias',
            'Version',
            'Provider',
            'Status',
            'Author',
        ];
        $result = [];

        $plugins = BaseHelper::scanFolder(plugin_path());
        if (! empty($plugins)) {
            $installed = get_active_plugins();
            foreach ($plugins as $plugin) {
                $configFile = plugin_path($plugin . '/plugin.json');
                if (! File::exists($configFile)) {
                    continue;
                }

                try {
                    $this->pluginService->validatePlugin($plugin, true);
                } catch (Throwable $exception) {
                    $this->error('Plugin ' . $plugin . ' is invalid!');
                    $this->error($exception->getMessage());
                }

                $content = BaseHelper::getFileData($configFile);
                if (! empty($content)) {
                    $result[] = [
                        Arr::get($content, 'name'),
                        $plugin,
                        Arr::get($content, 'version'),
                        Arr::get($content, 'provider'),
                        in_array($plugin, $installed) ? '✓ active' : '✘ inactive',
                        Arr::get($content, 'author'),
                    ];
                }
            }
        }

        $this->table($header, $result);

        return self::SUCCESS;
    }
}
