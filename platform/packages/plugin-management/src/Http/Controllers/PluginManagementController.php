<?php

namespace Botble\PluginManagement\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\PluginManagement\Services\MarketplaceService;
use Botble\PluginManagement\Services\PluginService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class PluginManagementController extends Controller
{
    public function __construct(protected PluginService $pluginService)
    {
        if (! config('packages.plugin-management.general.enable_plugin_manager', true)) {
            abort(404);
        }
    }

    public function index(): View
    {
        PageTitle::setTitle(trans('packages/plugin-management::plugin.plugins'));

        Assets::addScriptsDirectly('vendor/core/packages/plugin-management/js/plugin.js')
            ->addStylesDirectly('vendor/core/packages/plugin-management/css/plugin.css');

        $list = [];

        if (File::exists(plugin_path('.DS_Store'))) {
            File::delete(plugin_path('.DS_Store'));
        }

        $plugins = BaseHelper::scanFolder(plugin_path());

        if (! empty($plugins)) {
            $installed = get_active_plugins();
            foreach ($plugins as $plugin) {
                if (File::exists(plugin_path($plugin . '/.DS_Store'))) {
                    File::delete(plugin_path($plugin . '/.DS_Store'));
                }

                $pluginPath = plugin_path($plugin);

                if (! File::isDirectory($pluginPath) || ! File::exists($pluginPath . '/plugin.json')) {
                    continue;
                }

                $content = BaseHelper::getFileData($pluginPath . '/plugin.json');
                if (! empty($content)) {
                    if (! in_array($plugin, $installed)) {
                        $content['status'] = 0;
                    } else {
                        $content['status'] = 1;
                    }

                    $content['path'] = $plugin;
                    $content['image'] = null;

                    $screenshot = 'vendor/core/plugins/' . $plugin . '/screenshot.png';

                    if (File::exists(public_path($screenshot))) {
                        $content['image'] = asset($screenshot);
                    } elseif (File::exists($pluginPath . '/screenshot.png')) {
                        $content['image'] = 'data:image/png;base64,' . base64_encode(File::get($pluginPath . '/screenshot.png'));
                    }

                    $list[] = (object) $content;
                }
            }
        }

        return view('packages/plugin-management::index', compact('list'));
    }

    public function update(Request $request, BaseHttpResponse $response): BaseHttpResponse
    {
        $plugin = strtolower($request->input('name'));

        if (! $this->pluginService->validatePlugin($plugin)) {
            return $response
                ->setError()
                ->setMessage(trans('packages/plugin-management::plugin.invalid_plugin'));
        }

        try {
            $activatedPlugins = get_active_plugins();

            if (! in_array($plugin, $activatedPlugins)) {
                $result = $this->pluginService->activate($plugin);
            } else {
                $result = $this->pluginService->deactivate($plugin);
            }

            if ($result['error']) {
                return $response->setError()->setMessage($result['message']);
            }

            return $response->setMessage(trans('packages/plugin-management::plugin.update_plugin_status_success'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function destroy(string $plugin, BaseHttpResponse $response): BaseHttpResponse
    {
        $plugin = strtolower($plugin);

        if (! $this->pluginService->validatePlugin($plugin)) {
            return $response
                ->setError()
                ->setMessage(trans('packages/plugin-management::plugin.invalid_plugin'));
        }

        try {
            $result = $this->pluginService->remove($plugin);

            if ($result['error']) {
                return $response->setError()->setMessage($result['message']);
            }

            return $response->setMessage(trans('packages/plugin-management::plugin.remove_plugin_success'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function checkRequirement(
        Request $request,
        BaseHttpResponse $response,
        MarketplaceService $marketplaceService
    ): BaseHttpResponse {
        $name = strtolower($request->input('name'));

        $requiredPlugins = $this->pluginService->getDependencies($name);

        if (! empty($requiredPlugins)) {
            $content = $this->pluginService->getPluginInfo($name);

            $data = $marketplaceService->callApi('POST', '/products/check-update', [
                'products' => collect($requiredPlugins)->mapWithKeys(fn ($item) => [$item => '0.0.0'])->toArray(),
            ])->json('data');

            $existingPluginsOnMarketplace = collect($data)->pluck('id')->all();

            if (empty($existingPluginsOnMarketplace)) {
                return $response
                    ->setError()
                    ->setMessage(trans('packages/plugin-management::plugin.missing_required_plugins', [
                        'plugins' => implode(',', $requiredPlugins),
                    ]));
            }

            return $response
                ->setError()
                ->setData([
                    'pluginName' => $content['id'],
                    'existing_plugins_on_marketplace' => $existingPluginsOnMarketplace,
                ])
                ->setMessage(__('packages/plugin-management::plugin.requirement_not_met', [
                    'plugin' => "<strong>{$content['name']}</strong>",
                    'required_plugins' => '<strong>' . implode(', ', $requiredPlugins) . '</strong>',
                ]));
        }

        return $response;
    }
}
