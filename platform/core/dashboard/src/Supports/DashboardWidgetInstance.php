<?php

namespace Botble\Dashboard\Supports;

use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetSettingInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DashboardWidgetInstance
{
    protected string $type = 'widget';

    protected string $key;

    protected string $title;

    protected string $icon;

    protected string $color;

    protected string $route;

    protected string $bodyClass;

    protected bool $isEqualHeight = true;

    protected string|null $column = null;

    protected string $permission;

    protected int $statsTotal = 0;

    protected bool $hasLoadCallback = false;

    protected array $settings = [];

    protected array $predefinedRanges = [];

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getBodyClass(): string
    {
        return $this->bodyClass;
    }

    public function setBodyClass(string $bodyClass): self
    {
        $this->bodyClass = $bodyClass;

        return $this;
    }

    public function isEqualHeight(): bool
    {
        return $this->isEqualHeight;
    }

    public function setIsEqualHeight(bool $isEqualHeight): self
    {
        $this->isEqualHeight = $isEqualHeight;

        return $this;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $column): self
    {
        $this->column = $column;

        return $this;
    }

    public function getPermission(): string
    {
        return $this->permission;
    }

    public function setPermission(string $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    public function getStatsTotal(): int
    {
        return $this->statsTotal;
    }

    public function setStatsTotal(int $statsTotal): self
    {
        $this->statsTotal = $statsTotal;

        return $this;
    }

    public function isHasLoadCallback(): bool
    {
        return $this->hasLoadCallback;
    }

    public function setHasLoadCallback(bool $hasLoadCallback): self
    {
        $this->hasLoadCallback = $hasLoadCallback;

        return $this;
    }

    public function setSettings(array $settings): self
    {
        $this->settings = $settings;

        return $this;
    }

    public function init(array $widgets, Collection $widgetSettings): array
    {
        if (! Auth::user()->hasPermission($this->permission)) {
            return $widgets;
        }

        $widget = $widgetSettings->where('name', $this->key)->first();
        $widgetSetting = $widget ? $widget->settings->first() : null;

        if (! $widget) {
            $widget = app(DashboardWidgetInterface::class)
                ->firstOrCreate(['name' => $this->key]);
        }

        $widget->title = $this->title;
        $widget->icon = $this->icon;
        $widget->color = $this->color;
        $widget->route = $this->route;
        if ($this->type === 'widget') {
            $widget->bodyClass = $this->bodyClass;
            $widget->column = $this->column;

            $settings = array_merge(
                $widgetSetting && $widgetSetting->settings ? $widgetSetting->settings : [],
                $this->settings
            );
            $predefinedRanges = $this->getPredefinedRanges();

            $data = [
                'id' => $widget->id,
                'type' => $this->type,
                'view' => view(
                    'core/dashboard::widgets.base',
                    compact('widget', 'widgetSetting', 'settings', 'predefinedRanges')
                )->render(),
            ];

            if (empty($widgetSetting) || array_key_exists($widgetSetting->order, $widgets)) {
                $widgets[] = $data;
            } else {
                $widgets[$widgetSetting->order] = $data;
            }

            return $widgets;
        }

        $widget->statsTotal = $this->statsTotal;

        $widgets[$this->key] = [
            'id' => $widget->id,
            'type' => $this->type,
            'view' => view('core/dashboard::widgets.stats', compact('widget', 'widgetSetting'))->render(),
        ];

        return $widgets;
    }

    public function getPredefinedRanges(): array
    {
        return $this->predefinedRanges ?: $this->getPredefinedRangesDefault();
    }

    public function setPredefinedRanges(array $predefinedRanges): self
    {
        $this->predefinedRanges = $predefinedRanges;

        return $this;
    }

    public function getPredefinedRangesDefault(): array
    {
        $endDate = Carbon::today()->endOfDay();

        return [
            [
                'key' => 'today',
                'label' => trans('core/dashboard::dashboard.predefined_ranges.today'),
                'startDate' => Carbon::today()->startOfDay(),
                'endDate' => $endDate,
            ],
            [
                'key' => 'yesterday',
                'label' => trans('core/dashboard::dashboard.predefined_ranges.yesterday'),
                'startDate' => Carbon::yesterday()->startOfDay(),
                'endDate' => Carbon::yesterday()->endOfDay(),
            ],
            [
                'key' => 'this_week',
                'label' => trans('core/dashboard::dashboard.predefined_ranges.this_week'),
                'startDate' => Carbon::now()->startOfWeek(),
                'endDate' => Carbon::now()->endOfWeek(),
            ],
            [
                'key' => 'last_7_days',
                'label' => trans('core/dashboard::dashboard.predefined_ranges.last_7_days'),
                'startDate' => Carbon::now()->subDays(7)->startOfDay(),
                'endDate' => $endDate,
            ],
            [
                'key' => 'this_month',
                'label' => trans('core/dashboard::dashboard.predefined_ranges.this_month'),
                'startDate' => Carbon::now()->startOfMonth(),
                'endDate' => $endDate,
            ],
            [
                'key' => 'last_30_days',
                'label' => trans('core/dashboard::dashboard.predefined_ranges.last_30_days'),
                'startDate' => Carbon::now()->subDays(29)->startOfDay(),
                'endDate' => $endDate,
            ],
            [
                'key' => 'this_year',
                'label' => trans('core/dashboard::dashboard.predefined_ranges.this_year'),
                'startDate' => Carbon::now()->startOfYear(),
                'endDate' => $endDate,
            ],
        ];
    }

    public function getFilterRange(string|null $filterRangeInput)
    {
        $predefinedRanges = $this->getPredefinedRanges();
        $predefinedRanges = collect($predefinedRanges);

        if (! $filterRangeInput) {
            return $predefinedRanges->first();
        }

        $predefinedRangeFound = $predefinedRanges->firstWhere('key', $filterRangeInput);

        if ($predefinedRangeFound) {
            return $predefinedRangeFound;
        }

        return $predefinedRanges->first();
    }

    public function saveSettings(string $widgetName, array $settings): bool
    {
        $widget = app(DashboardWidgetInterface::class)->getFirstBy(['name' => $widgetName]);

        if (! $widget) {
            return false;
        }

        $widgetSetting = app(DashboardWidgetSettingInterface::class)->firstOrCreate([
            'widget_id' => $widget->id,
            'user_id' => Auth::id(),
        ]);

        $widgetSetting->settings = array_merge((array)$widgetSetting->settings, $settings);

        $widgetSetting->save();

        return true;
    }
}
