<?php

namespace Botble\Menu\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Menu\Facades\Menu;
use Botble\Menu\Forms\MenuForm;
use Botble\Menu\Http\Requests\MenuNodeRequest;
use Botble\Menu\Http\Requests\MenuRequest;
use Botble\Menu\Models\Menu as MenuModel;
use Botble\Menu\Models\MenuLocation;
use Botble\Menu\Models\MenuNode;
use Botble\Menu\Repositories\Eloquent\MenuRepository;
use Botble\Menu\Tables\MenuTable;
use Botble\Support\Services\Cache\Cache;
use Exception;
use Illuminate\Cache\CacheManager;
use Illuminate\Http\Request;
use stdClass;

class MenuController extends BaseController
{
    protected Cache $cache;

    public function __construct(CacheManager $cache)
    {
        $this->cache = new Cache($cache, MenuRepository::class);
    }

    public function index(MenuTable $table)
    {
        PageTitle::setTitle(trans('packages/menu::menu.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('packages/menu::menu.create'));

        return $formBuilder->create(MenuForm::class)->renderForm();
    }

    public function store(MenuRequest $request, BaseHttpResponse $response)
    {
        $menu = new MenuModel();

        $menu->fill($request->input());
        $menu->save();

        $this->cache->flush();

        event(new CreatedContentEvent(MENU_MODULE_SCREEN_NAME, $request, $menu));

        $this->saveMenuLocations($menu, $request);

        return $response
            ->setPreviousUrl(route('menus.index'))
            ->setNextUrl(route('menus.edit', $menu->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    protected function saveMenuLocations(MenuModel $menu, Request $request): bool
    {
        $locations = $request->input('locations', []);

        MenuLocation::query()
            ->where('menu_id', $menu->getKey())
            ->whereNotIn('location', $locations)
            ->each(fn (MenuLocation $location) => $location->delete());

        foreach ($locations as $location) {
            $menuLocation = MenuLocation::query()->firstOrCreate([
                'menu_id' => $menu->getKey(),
                'location' => $location,
            ]);

            event(new CreatedContentEvent(MENU_LOCATION_MODULE_SCREEN_NAME, $request, $menuLocation));
        }

        return true;
    }

    public function edit(int|string $id, FormBuilder $formBuilder, Request $request)
    {
        $oldInputs = old();
        if ($oldInputs && $id == 0) {
            $oldObject = new stdClass();
            foreach ($oldInputs as $key => $row) {
                $oldObject->$key = $row;
            }
            $menu = $oldObject;
        } else {
            $menu = MenuModel::query()->findOrFail($id);
        }

        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $menu->name]));

        event(new BeforeEditContentEvent($request, $menu));

        return $formBuilder->create(MenuForm::class, ['model' => $menu])->renderForm();
    }

    public function update(int|string $id, MenuRequest $request, BaseHttpResponse $response)
    {
        $menu = MenuModel::query()->findOrFail($id);

        $menu->fill($request->input());
        $menu->save();

        event(new UpdatedContentEvent(MENU_MODULE_SCREEN_NAME, $request, $menu));

        /**
         * @var MenuModel $menu
         */
        $this->saveMenuLocations($menu, $request);

        $deletedNodes = ltrim((string)$request->input('deleted_nodes', ''));
        if ($deletedNodes = explode(' ', $deletedNodes)) {
            $menu->menuNodes()->whereIn('id', $deletedNodes)->delete();
        }

        $menuNodes = Menu::recursiveSaveMenu((array)json_decode($request->input('menu_nodes'), true), $menu->getKey(), 0);

        $request->merge(['menu_nodes', json_encode($menuNodes)]);

        $this->cache->flush();

        return $response
            ->setPreviousUrl(route('menus.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(MenuModel $menu, Request $request, BaseHttpResponse $response)
    {
        try {
            $menu->delete();

            event(new DeletedContentEvent(MENU_MODULE_SCREEN_NAME, $request, $menu));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $menu = MenuModel::query()->findOrFail($id);
            $menu->delete();

            event(new DeletedContentEvent(MENU_MODULE_SCREEN_NAME, $request, $menu));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function getNode(MenuNodeRequest $request, BaseHttpResponse $response)
    {
        $data = (array)$request->input('data', []);

        $row = new MenuNode();
        $row->fill($data);
        $row = Menu::getReferenceMenuNode($data, $row);
        $row->save();

        event(new CreatedContentEvent(MENU_NODE_MODULE_SCREEN_NAME, $request, $row));

        $html = view('packages/menu::partials.node', compact('row'))->render();

        return $response
            ->setData(compact('html'))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }
}
