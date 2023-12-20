<?php

use Botble\Base\Facades\Action;
use Botble\Base\Facades\Filter;
use Illuminate\Support\Arr;

if (! function_exists('add_filter')) {
    function add_filter(
        string|array|null $hook,
        string|array|Closure $callback,
        int $priority = 20,
        int $arguments = 1
    ): void {
        Filter::addListener($hook, $callback, $priority, $arguments);
    }
}

if (! function_exists('remove_filter')) {
    function remove_filter(string $hook): void
    {
        Filter::removeListener($hook);
    }
}

if (! function_exists('add_action')) {
    function add_action(
        string|array|null $hook,
        string|array|Closure $callback,
        int $priority = 20,
        int $arguments = 1
    ): void {
        Action::addListener($hook, $callback, $priority, $arguments);
    }
}

if (! function_exists('apply_filters')) {
    function apply_filters(...$args)
    {
        return Filter::fire(array_shift($args), $args);
    }
}

if (! function_exists('do_action')) {
    function do_action(...$args): void
    {
        Action::fire(array_shift($args), $args);
    }
}

if (! function_exists('get_hooks')) {
    function get_hooks(string|null $name = null, bool $isFilter = true): array
    {
        if ($isFilter) {
            $listeners = Filter::getListeners();
        } else {
            $listeners = Action::getListeners();
        }

        if (empty($name)) {
            return $listeners;
        }

        return Arr::get($listeners, $name, []);
    }
}
