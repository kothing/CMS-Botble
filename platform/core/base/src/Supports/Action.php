<?php

namespace Botble\Base\Supports;

class Action extends ActionHookEvent
{
    public function fire(string $action, array $args): void
    {
        if (! $this->getListeners()) {
            return;
        }

        foreach ($this->getListeners() as $hook => $listeners) {
            krsort($listeners);
            foreach ($listeners as $arguments) {
                if ($hook !== $action) {
                    continue;
                }

                $parameters = [];
                for ($index = 0; $index < $arguments['arguments']; $index++) {
                    if (isset($args[$index])) {
                        $parameters[] = $args[$index];
                    }
                }
                call_user_func_array($this->getFunction($arguments['callback']), $parameters);
            }
        }
    }
}
