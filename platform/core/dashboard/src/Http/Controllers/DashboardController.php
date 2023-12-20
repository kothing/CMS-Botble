<?php

namespace Botble\Dashboard\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Dashboard\Models\DashboardWidget;
use Botble\Dashboard\Models\DashboardWidgetSetting;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function getDashboard(Request $request)
    {
        PageTitle::setTitle(trans('core/dashboard::dashboard.title'));

        Assets::addScripts(['blockui', 'sortable', 'equal-height', 'counterup'])
            ->addScriptsDirectly('vendor/core/core/dashboard/js/dashboard.js')
            ->addStylesDirectly('vendor/core/core/dashboard/css/dashboard.css');

        Assets::usingVueJS();

        do_action(DASHBOARD_ACTION_REGISTER_SCRIPTS);

        $widgets = DashboardWidget::query()
            ->with([
                'settings' => function (HasMany $query) use ($request) {
                    $query->where('user_id', $request->user()->getKey())
                        ->select(['status', 'order', 'settings', 'widget_id'])
                        ->orderBy('order');
                },
            ])
            ->select(['id', 'name'])
            ->get();

        $widgetData = apply_filters(DASHBOARD_FILTER_ADMIN_LIST, [], $widgets);
        ksort($widgetData);

        $availableWidgetIds = collect($widgetData)->pluck('id')->all();

        $widgets = $widgets->reject(function (DashboardWidget $item) use ($availableWidgetIds) {
            return ! in_array($item->getKey(), $availableWidgetIds);
        });

        $statWidgets = collect($widgetData)->where('type', '!=', 'widget')->pluck('view')->all();
        $userWidgets = collect($widgetData)->where('type', 'widget')->pluck('view')->all();

        return view('core/dashboard::list', compact('widgets', 'userWidgets', 'statWidgets'));
    }

    public function postEditWidgetSettingItem(Request $request, BaseHttpResponse $response)
    {
        try {
            $widget = DashboardWidget::query()->where([
                'name' => $request->input('name'),
            ])->first();

            if (! $widget) {
                return $response
                    ->setError()
                    ->setMessage(trans('core/dashboard::dashboard.widget_not_exists'));
            }

            $widgetSetting = DashboardWidgetSetting::query()->create([
                'widget_id' => $widget->getKey(),
                'user_id' => $request->user()->getKey(),
            ]);

            $widgetSetting->settings = array_merge((array)$widgetSetting->settings, [
                $request->input('setting_name') => $request->input('setting_value'),
            ]);

            $widgetSetting->save();
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }

        return $response;
    }

    public function postUpdateWidgetOrder(Request $request, BaseHttpResponse $response)
    {
        foreach ($request->input('items', []) as $key => $item) {
            $widget = DashboardWidget::query()->firstOrCreate([
                'name' => $item,
            ]);

            $widgetSetting = DashboardWidgetSetting::query()->firstOrCreate([
                'widget_id' => $widget->getKey(),
                'user_id' => $request->user()->getKey(),
            ]);

            $widgetSetting->order = $key;
            $widgetSetting->save();
        }

        return $response->setMessage(trans('core/dashboard::dashboard.update_position_success'));
    }

    public function getHideWidget(Request $request, BaseHttpResponse $response)
    {
        $widget = DashboardWidget::query()->where([
            'name' => $request->input('name'),
        ], ['id'])->first();

        if (! empty($widget)) {
            $widgetSetting = DashboardWidgetSetting::query()->firstOrCreate([
                'widget_id' => $widget->getKey(),
                'user_id' => $request->user()->getKey(),
            ]);

            $widgetSetting->status = 0;
            $widgetSetting->order = 99 + $widgetSetting->getKey();
            $widgetSetting->save();
        }

        return $response->setMessage(trans('core/dashboard::dashboard.hide_success'));
    }

    public function postHideWidgets(Request $request, BaseHttpResponse $response)
    {
        $widgets = DashboardWidget::query()->get();

        foreach ($widgets as $widget) {
            $widgetSetting = DashboardWidgetSetting::query()->firstOrCreate([
                'widget_id' => $widget->getKey(),
                'user_id' => $request->user()->getKey(),
            ]);

            if ($request->has('widgets.' . $widget->name) &&
                $request->input('widgets.' . $widget->name) == 1
            ) {
                $widgetSetting->status = 1;
            } else {
                $widgetSetting->status = 0;
                $widgetSetting->order = 99 + $widgetSetting->getKey();
            }

            $widgetSetting->save();
        }

        return $response
            ->setNextUrl(route('dashboard.index'))
            ->setMessage(trans('core/dashboard::dashboard.hide_success'));
    }
}
