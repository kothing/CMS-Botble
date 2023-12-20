<?php

namespace Botble\PluginManagement\Commands;

use Botble\PluginManagement\Services\MarketplaceService;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Throwable;

#[AsCommand('cms:plugin:install-from-marketplace', 'Install a plugin from https://marketplace.botble.com')]
class PluginInstallFromMarketplaceCommand extends Command
{
    public function handle(MarketplaceService $marketplaceService): int
    {
        $plugin = strtolower($this->argument('name'));

        if (! preg_match('/^[a-z0-9\-_\/]+$/i', $plugin)) {
            $this->components->error('Only alphabetic characters are allowed.');

            exit(self::FAILURE);
        }

        $response = $marketplaceService->callApi('post', '/products/check-update', [
            'products' => [$plugin => '0.0.0'],
        ]);

        if ($response->failed()) {
            $this->error($response->reason());

            return self::FAILURE;
        }

        $pluginId = $response->json('data.0.id');

        if (! $pluginId) {
            $this->error(sprintf('Plugin %s doesnt exists', $plugin));

            return self::FAILURE;
        }

        $response = $marketplaceService->callApi('get', '/products/' . $pluginId);

        if ($response instanceof JsonResponse) {
            $this->error($response->getData()->message);

            return self::FAILURE;
        }

        if ($response->failed()) {
            $this->error($response->reason());

            return self::FAILURE;
        }

        $detail = $response->json();

        $version = $detail['data']['minimum_core_version'];
        if (version_compare($version, get_core_version(), '>')) {
            $this->error(trans('packages/plugin-management::marketplace.minimum_core_version_error', compact('version')));

            return self::FAILURE;
        }

        $name = Str::afterLast($detail['data']['package_name'], '/');

        try {
            $response = $marketplaceService->beginInstall($pluginId, 'plugin', $name);

            if ($response instanceof JsonResponse) {
                $this->error($response->getData()->message);

                return self::FAILURE;
            }

            $this->components->info(sprintf('Installed plugin %s successfully', $plugin));

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The plugin that you want to install');
    }
}
