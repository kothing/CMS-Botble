<?php

namespace Botble\PluginManagement\Abstracts;

abstract class PluginOperationAbstract
{
    public static function activate()
    {
        // Run when activating a plugin
    }

    public static function activated()
    {
        // Run when a plugin is activated
    }

    public static function deactivate()
    {
        // Run when deactivating a plugin
    }

    public static function deactivated()
    {
        // Run when a plugin is deactivated
    }

    public static function remove()
    {
        // Run when remove a plugin
    }

    public static function removed()
    {
        // Run when removed a plugin
    }
}
