<?php

namespace Botble\RequestLog\Commands;

use Botble\RequestLog\Repositories\Interfaces\RequestLogInterface;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:request-logs:clear', 'Clear all request error logs')]
class RequestLogClearCommand extends Command
{
    public function handle(RequestLogInterface $requestLogRepository): int
    {
        $this->components->info('Processing...');

        $count = $requestLogRepository->count();
        $requestLogRepository->getModel()->truncate();

        $this->components->info('Done. Deleted ' . $count . ' records!');

        return self::SUCCESS;
    }
}
