<?php

namespace Botble\Widget\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Widget\Facades\WidgetGroup;
use Botble\Widget\Models\Widget;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WidgetController extends BaseController
{
    public function index()
    {
        PageTitle::setTitle(trans('packages/widget::widget.name'));

        Assets::addScripts(['sortable'])
            ->addScriptsDirectly('vendor/core/packages/widget/js/widget.js');

        $widgets = Widget::query()->where('theme', Widget::getThemeName())->get();

        $groups = WidgetGroup::getGroups();
        foreach ($widgets as $widget) {
            if (Arr::has($groups, $widget->sidebar_id)) {
                WidgetGroup::group($widget->sidebar_id)
                    ->position($widget->position)
                    ->addWidget($widget->widget_id, $widget->data);
            }
        }

        return view('packages/widget::list');
    }

    public function update(Request $request, BaseHttpResponse $response)
    {
        try {
            $sidebarId = $request->input('sidebar_id');

            $themeName = Widget::getThemeName();

            Widget::query()->where([
                'sidebar_id' => $sidebarId,
                'theme' => $themeName,
            ])->delete();

            foreach ($request->input('items', []) as $key => $item) {
                parse_str($item, $data);
                if (empty($data['id'])) {
                    continue;
                }

                Widget::query()->create([
                    'sidebar_id' => $sidebarId,
                    'widget_id' => $data['id'],
                    'theme' => $themeName,
                    'position' => $key,
                    'data' => $data,
                ]);
            }

            $widgetAreas = Widget::query()->where([
                'sidebar_id' => $sidebarId,
                'theme' => $themeName,
            ])->get();

            return $response
                ->setData(view('packages/widget::item', compact('widgetAreas'))->render())
                ->setMessage(trans('packages/widget::widget.save_success'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function destroy(Request $request, BaseHttpResponse $response)
    {
        try {
            Widget::query()->where([
                'theme' => Widget::getThemeName(),
                'sidebar_id' => $request->input('sidebar_id'),
                'position' => $request->input('position'),
                'widget_id' => $request->input('widget_id'),
            ])->delete();

            return $response->setMessage(trans('packages/widget::widget.delete_success'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
