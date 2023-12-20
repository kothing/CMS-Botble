<?php

namespace Botble\PluginManagement\Commands;

use Illuminate\Foundation\Console\PackageDiscoverCommand as IlluminatePackageDiscoverCommand;
use Illuminate\Foundation\PackageManifest;

class PackageDiscoverCommand extends IlluminatePackageDiscoverCommand
{
    public function handle(PackageManifest $manifest): int
    {
        parent::handle($manifest);

        $this->call(PluginDiscoverCommand::class);

        return self::SUCCESS;
    }
}
