<?php

namespace Botble\Base\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:install', 'Install CMS')]
class InstallCommand extends Command
{
    use ConfirmableTrait;

    public function handle(): int
    {
        $this->newLine();

        $this->components->info('Starting installation...');

        $this->components->info('Running migrate...');
        $this->call('migrate:fresh');
        $this->components->info('Migrate done!');

        if ($this->confirmToProceed('Create a new super user?', true)) {
            $this->call('cms:user:create');
        }

        if ($this->confirmToProceed('Do you want to activate all plugins?', true)) {
            $this->components->info('Activating all plugins...');
            $this->call('cms:plugin:activate:all');
            $this->components->info('All plugins are activated!');
        }

        if ($this->confirmToProceed('Do you want to install sample data?', true)) {
            $this->components->info('Seeding...');
            $this->call('db:seed');
            $this->components->info('Seeding done!');
        }

        $this->components->info('Publishing assets...');
        $this->call('cms:publish:assets');
        $this->components->info('Publishing assets done!');

        $this->components->info('Publishing lang...');
        $this->call('vendor:publish', ['--tag' => 'cms-lang']);
        $this->components->info('Publishing lang done!');

        $this->components->info('Install CMS successfully!');

        return self::SUCCESS;
    }
}
