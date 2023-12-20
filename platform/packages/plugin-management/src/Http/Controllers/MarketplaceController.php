<?php

namespace Botble\PluginManagement\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\PluginManagement\Services\MarketplaceService;
use Botble\PluginManagement\Services\PluginService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Throwable;

class MarketplaceController extends Controller
{
    public function __construct(
        protected MarketplaceService $marketplaceService,
        protected PluginService $pluginService,
    ) {
    }

    public function index(): View
    {
        PageTitle::setTitle(trans('packages/plugin-management::plugin.plugins_add_new'));

        Assets::addScriptsDirectly('vendor/core/packages/plugin-management/js/marketplace.js');

        Assets::usingVueJS();

        return view('packages/plugin-management::marketplace.index');
    }

    public function list(Request $request, BaseHttpResponse $httpResponse): array|BaseHttpResponse
    {
        $request->merge([
            'type' => 'plugin',
            'per_page' => 12,
        ]);

        $response = $this->marketplaceService->callApi('get', '/products', $request->input());

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
        } else {
            $data = $response->json();
        }

        if (isset($data['error']) && $data['error']) {
            return $httpResponse
                ->setError()
                ->setMessage($data['message']);
        }

        $coreVersion = get_core_version();

        foreach ($data['data'] as $key => $item) {
            $data['data'][$key]['version_check'] = version_compare($coreVersion, $item['minimum_core_version'], '>=');
            $data['data'][$key]['humanized_last_updated_at'] = Carbon::parse($item['last_updated_at'])->diffForHumans();
        }

        return $data;
    }

    public function detail(string $id): JsonResponse|array|null
    {
        $response = $this->marketplaceService->callApi('get', '/products/' . $id);

        if ($response instanceof JsonResponse) {
            return $response;
        }

        return $response->json();
    }

    public function iframe(string $id): JsonResponse|string
    {
        $response = $this->marketplaceService->callApi('get', '/products/' . $id . '/iframe');

        if ($response instanceof JsonResponse) {
            return $response;
        }

        return $response->body();
    }

    public function install(string $id): JsonResponse
    {
        $detail = $this->detail($id);

        $version = $detail['data']['minimum_core_version'];
        if (version_compare($version, get_core_version(), '>')) {
            return response()->json([
                'error' => true,
                'message' => trans('packages/plugin-management::marketplace.minimum_core_version_error', compact('version')),
            ]);
        }

        $name = Str::afterLast($detail['data']['package_name'], '/');

        try {
            $this->marketplaceService->beginInstall($id, 'plugin', $name);
        } catch (Throwable $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'error' => false,
            'message' => trans('packages/plugin-management::marketplace.install_success'),
            'data' => [
                'name' => $name,
                'id' => $id,
            ],
        ]);
    }

    public function update(string $id, string|null $name = null): JsonResponse
    {
        $detail = $this->detail($id);

        if (! $name) {
            $name = Str::afterLast($detail['data']['package_name'], '/');
        }

        try {
            $this->marketplaceService->beginInstall($id, 'plugin', $name);
        } catch (Throwable $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ]);
        }

        $this->pluginService->runMigrations($name);

        $published = $this->pluginService->publishAssets($name);

        if ($published['error']) {
            return response()->json([
                'error' => true,
                'message' => $published['message'],
            ]);
        }

        $this->pluginService->publishTranslations($name);

        return response()->json([
            'error' => false,
            'message' => trans('packages/plugin-management::marketplace.update_success'),
            'data' => [
                'name' => $name,
                'id' => $id,
            ],
        ]);
    }

    public function checkUpdate(): JsonResponse|array|null
    {
        $installedPlugins = $this->pluginService->getInstalledPluginIds();

        if (! $installedPlugins) {
            return response()->json();
        }

        $response = $this->marketplaceService->callApi('post', '/products/check-update', [
            'products' => $installedPlugins,
        ]);

        return $response instanceof JsonResponse ? $response : $response->json();
    }
}
