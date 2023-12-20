<?php

namespace Botble\DevTool\Commands\Make;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:make:repository', 'Make a repository')]
class RepositoryMakeCommand extends BaseMakeCommand
{
    public function handle(): int
    {
        if (! preg_match('/^[a-z0-9\-_]+$/i', $this->argument('name'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $name = $this->argument('name');

        $stub = $this->getStub();
        $path = platform_path(strtolower($this->argument('module')) . '/src/Repositories');

        $this->publishFile($stub . '/Interfaces/{Name}Interface.stub', $path, $name, 'Interfaces', 'Interface.php');
        $this->publishFile($stub . '/Eloquent/{Name}Repository.stub', $path, $name, 'Eloquent', 'Repository.php');
        $this->publishFile($stub . '/Caches/{Name}CacheDecorator.stub', $path, $name, 'Caches', 'CacheDecorator.php');

        $this->line('------------------');

        $this->components->info('Created successfully <comment>' . $path . '</comment>!');

        return self::SUCCESS;
    }

    protected function publishFile(string $stub, string $path, string $name, string $prefix, string $extension): void
    {
        $path = $path . '/' . $prefix . '/' . ucfirst(Str::studly($name)) . $extension;

        $this->publishStubs($stub, $path);
        $this->renameFiles($stub, $path);
        $this->searchAndReplaceInFiles($name, $path, $this->laravel['files']->get($stub));
    }

    public function getStub(): string
    {
        return __DIR__ . '/../../../stubs/module/src/Repositories';
    }

    public function getReplacements(string $replaceText): array
    {
        $module = explode('/', $this->argument('module'));
        $module = strtolower(end($module));

        return [
            '{Module}' => ucfirst(Str::camel($module)),
        ];
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The repository name that you want to create');
        $this->addArgument('module', InputArgument::REQUIRED, 'The module name');
    }
}
