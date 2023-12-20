<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:package:create', 'Create a package in the /platform/packages directory.')]
class PackageCreateCommand extends BaseMakeCommand
{
    public function handle(): int
    {
        if (! preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $package = strtolower($this->argument('name'));
        $location = package_path($package);

        if ($this->laravel['files']->isDirectory($location)) {
            $this->components->error('A package named [' . $package . '] already exists.');

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $location);
        $this->renameFiles($package, $location);
        $this->searchAndReplaceInFiles($package, $location);
        $this->line('------------------');
        $this->line(
            '<info>The package</info> <comment>' . $package . '</comment> <info>was created in</info> <comment>' . $location . '</comment><info>, customize it!</info>'
        );
        $this->line(
            '<info>Add</info> <comment>"botble/' . $package . '": "*@dev"</comment> to composer.json then run <comment>composer update</comment> to install this package!'
        );
        $this->line('------------------');
        $this->call('cache:clear');

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return __DIR__ . '/../../stubs/module';
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{-module}' => strtolower($replaceText),
            '{module}' => Str::snake(str_replace('-', '_', $replaceText)),
            '{+module}' => Str::camel($replaceText),
            '{modules}' => Str::plural(Str::snake(str_replace('-', '_', $replaceText))),
            '{Modules}' => ucfirst(Str::plural(Str::snake(str_replace('-', '_', $replaceText)))),
            '{-modules}' => Str::plural($replaceText),
            '{MODULE}' => strtoupper(Str::snake(str_replace('-', '_', $replaceText))),
            '{Module}' => ucfirst(Str::camel($replaceText)),
        ];
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The package name that you want to create');
    }
}
