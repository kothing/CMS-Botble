<?php

namespace Botble\PluginManagement\Listeners;

use Botble\PluginManagement\PluginManifest;
use Exception;
use Illuminate\Support\Facades\File;

class ClearPluginCaches
{
    public function __construct(protected PluginManifest $manifest)
    {
    }

    public function handle(): void
    {
        try {
            if (File::isFile($pluginsPath = $this->manifest->getManifestFilePath())) {
                File::delete($pluginsPath);
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
