<?php

namespace Botble\PluginManagement\Commands;

use Illuminate\Foundation\Console\ClearCompiledCommand as BaseClearCompiledCommand;

class IlluminateClearCompiledCommand extends BaseClearCompiledCommand
{
    public function handle(): int
    {
        parent::handle();

        $this->call(ClearCompiledCommand::class);

        return self::SUCCESS;
    }
}
