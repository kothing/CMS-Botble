<?php

namespace Botble\DevTool\Commands\Make;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:make:route', 'Make a route')]
class RouteMakeCommand extends BaseMakeCommand
{
    public function handle(): int
    {
        if (! preg_match('/^[a-z0-9\-_]+$/i', $this->argument('name'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $name = $this->argument('name');
        $path = platform_path(strtolower($this->argument('module')) . '/routes/' . strtolower($name) . '.php');

        $this->publishStubs($this->getStub(), $path);
        $this->renameFiles($name, $path);
        $this->searchAndReplaceInFiles($name, $path);
        $this->line('------------------');

        $this->components->info('Created successfully <comment>' . $path . '</comment>!');

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return __DIR__ . '/../../../stubs/module/routes/web.stub';
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
        $this->addArgument('name', InputArgument::REQUIRED, 'The route name that you want to create');
        $this->addArgument('module', InputArgument::REQUIRED, 'The module name');
    }
}
