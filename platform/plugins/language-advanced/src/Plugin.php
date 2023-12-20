<?php

namespace Botble\LanguageAdvanced;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Facades\Setting;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function activated(): void
    {
        $plugins = get_active_plugins();

        $isPluginLanguageActivated = is_plugin_active('language');

        if ($isPluginLanguageActivated && ($key = array_search('language', $plugins)) !== false) {
            unset($plugins[$key]);
        }

        if (($key = array_search('language-advanced', $plugins)) !== false) {
            unset($plugins[$key]);
        }

        array_unshift($plugins, 'language-advanced');

        if ($isPluginLanguageActivated) {
            array_unshift($plugins, 'language');
        }

        Setting::set('activated_plugins', json_encode($plugins))->save();
    }

    public static function remove(): void
    {
        Schema::dropIfExists('pages_translations');
        Schema::dropIfExists('slugs_translations');
    }
}
