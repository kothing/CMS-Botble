<?php

namespace Botble\PluginManagement\Commands\Concern;

trait HasPluginNameValidation
{
    protected function validatePluginName(string $name): void
    {
        if (! preg_match('/^[a-z0-9\-_.]+$/i', $name)) {
            $this->components->error('Only alphabetic characters are allowed.');

            exit(self::FAILURE);
        }
    }
}
