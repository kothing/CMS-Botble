<?php

namespace Botble\Gallery;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('gallery_meta');
        Schema::dropIfExists('galleries_translations');
        Schema::dropIfExists('gallery_meta_translations');
    }
}
