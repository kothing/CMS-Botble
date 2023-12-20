<?php

namespace Botble\Base\Http\Controllers;

use Botble\Base\Events\UpdatedEvent;
use Botble\Base\Events\UpdatingEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Services\CleanDatabaseService;
use Botble\Base\Services\ClearCacheService;
use Botble\Base\Supports\Core;
use Botble\Base\Supports\Language;
use Botble\Base\Supports\MembershipAuthorization;
use Botble\Base\Supports\SystemManagement;
use Botble\Base\Tables\InfoTable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class SystemController extends Controller
{
    public function getInfo(Request $request, InfoTable $infoTable)
    {
        PageTitle::setTitle(trans('core/base::system.info.title'));

        Assets::addScriptsDirectly('vendor/core/core/base/js/system-info.js')
            ->addStylesDirectly(['vendor/core/core/base/css/system-info.css']);

        $composerArray = SystemManagement::getComposerArray();
        $packages = SystemManagement::getPackagesAndDependencies($composerArray['require']);

        if ($request->expectsJson()) {
            return $infoTable->renderTable();
        }

        $systemEnv = SystemManagement::getSystemEnv();
        $serverEnv = SystemManagement::getServerEnv();

        $requiredPhpVersion = Arr::get($composerArray, 'require.php', get_minimum_php_version());
        $requiredPhpVersion = str_replace('^', '', $requiredPhpVersion);
        $requiredPhpVersion = str_replace('~', '', $requiredPhpVersion);

        $matchPHPRequirement = version_compare(phpversion(), $requiredPhpVersion, '>=') > 0;

        return view(
            'core/base::system.info',
            compact(
                'packages',
                'infoTable',
                'systemEnv',
                'serverEnv',
                'matchPHPRequirement',
                'requiredPhpVersion'
            )
        );
    }

    public function getCacheManagement()
    {
        PageTitle::setTitle(trans('core/base::cache.cache_management'));

        Assets::addScriptsDirectly('vendor/core/core/base/js/cache.js');

        return view('core/base::system.cache');
    }

    public function postClearCache(Request $request, BaseHttpResponse $response, ClearCacheService $clearCacheService)
    {
        $request->validate([
            'type' => ['required', 'string', Rule::in([
                'clear_cms_cache',
                'refresh_compiled_views',
                'clear_config_cache',
                'clear_route_cache',
                'clear_log',
            ])],
        ]);

        $type = $request->input('type');

        switch ($type) {
            case 'clear_cms_cache':
                $clearCacheService->clearFrameworkCache();
                $clearCacheService->clearGoogleFontsCache();
                $clearCacheService->clearMenuCache();
                $clearCacheService->clearPurifier();
                $clearCacheService->clearDebugbar();

                break;
            case 'refresh_compiled_views':
                $clearCacheService->clearCompiledViews();

                break;
            case 'clear_config_cache':
                $clearCacheService->clearConfig();

                break;
            case 'clear_route_cache':
                $clearCacheService->clearRoutesCache();

                break;
            case 'clear_log':
                $clearCacheService->clearLogs();

                break;
        }

        return $response->setMessage(trans('core/base::cache.commands.' . $type . '.success_msg'));
    }

    public function authorize(MembershipAuthorization $authorization, BaseHttpResponse $response)
    {
        $authorization->authorize();

        return $response;
    }

    public function getLanguage(string $lang, Request $request)
    {
        if ($lang && array_key_exists($lang, Language::getAvailableLocales())) {
            if (Auth::check()) {
                cache()->forget(md5('cache-dashboard-menu-' . $request->user()->getKey()));
            }
            session()->put('site-locale', $lang);
        }

        return redirect()->back();
    }

    public function getMenuItemsCount(BaseHttpResponse $response)
    {
        $data = apply_filters(BASE_FILTER_MENU_ITEMS_COUNT, []);

        return $response->setData($data);
    }

    public function getCheckUpdate(BaseHttpResponse $response, Core $core)
    {
        if (! config('core.base.general.enable_system_updater')) {
            return $response;
        }

        $response->setData(['has_new_version' => false]);

        $updateData = $core->checkUpdate();

        if ($updateData) {
            $response
                ->setData(['has_new_version' => true])
                ->setMessage(
                    sprintf('A new version (%s / released on %s) is available to update', $updateData->version, $updateData->releasedDate->toDateString())
                );
        }

        return $response;
    }

    public function getUpdater(Core $core)
    {
        if (! config('core.base.general.enable_system_updater')) {
            abort(404);
        }

        header('Cache-Control: no-cache');

        Assets::addScriptsDirectly('vendor/core/core/base/js/system-update.js');
        Assets::usingVueJS();

        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        PageTitle::setTitle(trans('core/base::system.updater'));

        $isOutdated = false;

        $latestUpdate = $core->getLatestVersion();

        if ($latestUpdate) {
            $isOutdated = version_compare($core->version(), $latestUpdate->version, '<');
        }

        $updateData = ['message' => null, 'status' => false];

        return view('core/base::system.updater', compact('latestUpdate', 'isOutdated', 'updateData'));
    }

    public function postUpdater(Core $core, Request $request, BaseHttpResponse $response): BaseHttpResponse
    {
        $request->validate([
            'step' => ['required', 'integer', 'min:1', 'max:4'],
            'update_id' => ['required', 'string'],
            'version' => ['required', 'string'],
        ]);

        $updateId = $request->input('update_id');
        $version = $request->input('version');

        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        try {
            switch ($request->integer('step', 1)) {
                case 1:
                    event(new UpdatingEvent());

                    if ($core->downloadUpdate($updateId, $version)) {
                        return $response->setMessage(__('The update files have been downloaded successfully.'));
                    }

                    return $response
                        ->setMessage(__('Could not download updated file. Please check your license or your internet network.'))
                        ->setError()
                        ->setCode(422);

                case 2:
                    if ($core->updateFilesAndDatabase($version)) {
                        return $response->setMessage(__('Your files and database have been updated successfully.'));
                    }

                    return $response
                        ->setMessage(__('Could not update files & database.'))
                        ->setError()
                        ->setCode(422);
                case 3:
                    $core->publishUpdateAssets();

                    return $response->setMessage(__('Your asset files have been published successfully.'));
                case 4:
                    $core->cleanCaches();

                    event(new UpdatedEvent());

                    return $response->setMessage(__('Your system has been cleaned up successfully.'));
            }
        } catch (Throwable $exception) {
            $core->logError($exception);

            return $response
                ->setMessage($exception->getMessage() . ' - ' . $exception->getFile() . ':' . $exception->getLine())
                ->setError()
                ->setCode(422);
        }

        return $response
            ->setMessage(__('Something went wrong.'))
            ->setError()
            ->setCode(422);
    }

    public function getCleanup(
        Request $request,
        BaseHttpResponse $response,
        CleanDatabaseService $cleanDatabaseService
    ): BaseHttpResponse|View {
        PageTitle::setTitle(trans('core/base::system.cleanup.title'));

        Assets::addScriptsDirectly('vendor/core/core/base/js/cleanup.js');

        try {
            $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        } catch (Throwable) {
            $tables = [];
        }

        $disabledTables = [
            'disabled' => $cleanDatabaseService->getIgnoreTables(),
            'checked' => [],
        ];

        if ($request->isMethod('POST')) {
            if (! config('core.base.general.enabled_cleanup_database', false)) {
                return $response
                    ->setCode(401)
                    ->setError()
                    ->setMessage(strip_tags(trans('core/base::system.cleanup.not_enabled_yet')));
            }

            $request->validate(['tables' => 'array']);

            $cleanDatabaseService->execute($request->input('tables', []));

            return $response->setMessage(trans('core/base::system.cleanup.success_message'));
        }

        return view('core/base::system.cleanup', compact('tables', 'disabledTables'));
    }
}
