<?php

namespace Botble\DevTool\Commands\Make;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:make:request', 'Make a request')]
class RequestMakeCommand extends BaseMakeCommand
{
    public function handle(): int
    {
        if (! preg_match('/^[a-z0-9\-_]+$/i', $this->argument('name'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $name = $this->argument('name');
        $path = platform_path(
            strtolower($this->argument('module')) . '/src/Http/Requests/' . ucfirst(Str::studly($name)) . 'Request.php'
        );

        $this->publishStubs($this->getStub(), $path);
        $this->renameFiles($name, $path);
        $this->searchAndReplaceInFiles($name, $path);
        $this->line('------------------');

        $this->components->info('Created successfully <comment>' . $path . '</comment>!');

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return __DIR__ . '/../../../stubs/module/src/Http/Requests/{Name}Request.stub';
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
        $this->addArgument('name', InputArgument::REQUIRED, 'The request name that you want to create');
        $this->addArgument('module', InputArgument::REQUIRED, 'The module name');
    }
}
