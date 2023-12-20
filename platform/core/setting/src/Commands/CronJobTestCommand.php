<?php

namespace Botble\Setting\Commands;

use Botble\Setting\Facades\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:cronjob:test', 'Run test cronjob')]
class CronJobTestCommand extends Command
{
    public function handle(): int
    {
        Setting::set('cronjob_last_run_at', Carbon::now())->save();

        return self::SUCCESS;
    }
}
