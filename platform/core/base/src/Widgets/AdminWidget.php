<?php

namespace Botble\Base\Widgets;

use Botble\Base\Events\RenderingAdminWidgetEvent;
use Botble\Base\Widgets\Contracts\AdminWidget as AdminWidgetContract;
use Illuminate\Contracts\View\View;

class AdminWidget implements AdminWidgetContract
{
    protected array $widgets = [];

    protected string $namespace = 'global';

    public function register(array $widgets, string|null $namespace): static
    {
        foreach ($widgets as $key => $widget) {
            $this->widgets[$namespace][is_string($key) ? $key : $widget] = $widget;
        }

        return $this;
    }

    public function remove(string $id, string|null $namespace): static
    {
        unset($this->widgets[$namespace][$id]);

        return $this;
    }

    public function getColumns(string|null $namespace): int
    {
        return match ($count = count($this->widgets[$namespace])) {
            5, 6, 9, 11 => 3,
            7, 8, 10, 12 => 4,
            default => $count,
        };
    }

    public function render(string $namespace): View
    {
        event(new RenderingAdminWidgetEvent($this));

        $widgets = collect();

        foreach ($this->widgets[$namespace] as $widget) {
            $widgets->add(resolve($widget));
        }

        $widgets = $widgets->sortBy(fn (Widget $widget) => $widget->getPriority())->toArray();

        return view('core/base::widgets.render', [
            'widgets' => $widgets,
            'columns' => $this->getColumns($namespace),
        ]);
    }
}
