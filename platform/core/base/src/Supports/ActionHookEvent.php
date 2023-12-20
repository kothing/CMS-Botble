<?php

namespace Botble\Base\Supports;

use Closure;
use Illuminate\Support\Arr;

abstract class ActionHookEvent
{
    protected array $listeners = [];

    public function addListener(
        string|array|null $hook,
        string|array|Closure $callback,
        int $priority = 20,
        int $arguments = 1
    ): void {
        if (! is_array($hook)) {
            $hook = [$hook];
        }

        foreach ($hook as $hookName) {
            while (isset($this->listeners[$hookName][$priority])) {
                $priority += 1;
            }

            $this->listeners[$hookName][$priority] = compact('callback', 'arguments');
        }
    }

    public function removeListener(string $hook): self
    {
        Arr::forget($this->listeners, $hook);

        return $this;
    }

    public function getListeners(): array
    {
        foreach ($this->listeners as $listeners) {
            uksort($listeners, function ($param1, $param2) {
                return strnatcmp($param1, $param2);
            });
        }

        return $this->listeners;
    }

    protected function getFunction(string|array|Closure|null $callback): bool|array|Closure|string
    {
        if (is_string($callback)) {
            if (strpos($callback, '@')) {
                $callback = explode('@', $callback);

                return [app('\\' . $callback[0]), $callback[1]];
            }

            return $callback;
        } elseif ($callback instanceof Closure) {
            return $callback;
        } elseif (is_array($callback)) {
            return $callback;
        }

        return false;
    }

    abstract public function fire(string $action, array $args);
}
