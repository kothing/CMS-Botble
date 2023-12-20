<?php

namespace Botble\Slug\Commands;

use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('cms:slug:prefix', 'Change/set prefix for slugs')]
class ChangeSlugPrefixCommand extends Command
{
    public function handle(): int
    {
        $data = app(SlugInterface::class)->update(
            ['reference_type' => $this->argument('class')],
            ['prefix' => $this->option('prefix') ?? '']
        );

        $this->components->info('Processed ' . $data . ' item(s)!');

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('class', InputArgument::REQUIRED, 'The model class');
        $this->addOption('prefix', null, InputOption::VALUE_REQUIRED, 'The prefix of slugs');
    }
}
