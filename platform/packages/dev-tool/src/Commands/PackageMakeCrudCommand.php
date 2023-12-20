<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:package:make:crud', 'Create a CRUD inside a package')]
class PackageMakeCrudCommand extends BaseMakeCommand
{
    public function handle(): int
    {
        if (! preg_match('/^[a-z0-9\-]+$/i', $this->argument('package')) || ! preg_match(
            '/^[a-z0-9\-]+$/i',
            $this->argument('name')
        )) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $package = strtolower($this->argument('package'));
        $location = package_path($package);

        if (! $this->laravel['files']->isDirectory($location)) {
            $this->components->error('Plugin named [' . $package . '] does not exists.');

            return self::FAILURE;
        }

        $name = strtolower($this->argument('name'));

        $this->publishStubs($this->getStub(), $location);
        $this->removeUnusedFiles($location);
        $this->renameFiles($name, $location);
        $this->searchAndReplaceInFiles($name, $location);
        $this->line('------------------');
        $this->line(
            '<info>The CRUD for package </info> <comment>' . $package . '</comment> <info>was created in</info> <comment>' . $location . '</comment><info>, customize it!</info>'
        );
        $this->line('------------------');
        $this->call('cache:clear');

        $replacements = [
            'config/permissions.stub',
            'helpers/constants.stub',
            'routes/web.stub',
            'src/Providers/{Module}ServiceProvider.stub',
        ];

        foreach ($replacements as $replacement) {
            $this->line(
                'Add below code into ' . $this->replacementSubModule(
                    null,
                    str_replace(base_path(), '', $location) . '/' . $replacement
                )
            );

            $this->info($this->replacementSubModule($replacement));
        }

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return __DIR__ . '/../../../dev-tool/stubs/module';
    }

    protected function removeUnusedFiles(string $location)
    {
        $files = [
            'config/permissions.stub',
            'helpers/constants.stub',
            'routes/web.stub',
            'src/Providers/{Module}ServiceProvider.stub',
        ];

        foreach ($files as $file) {
            $this->laravel['files']->delete($location . '/' . $file);
        }
    }

    protected function replacementSubModule(string $file = null, string|null $content = null): string
    {
        $name = strtolower($this->argument('name'));

        if ($file && empty($content)) {
            $content = file_get_contents($this->getStub() . '/../sub-module/' . $file);
        }

        $replace = $this->getReplacements($name) + $this->baseReplacements($name);

        return str_replace(array_keys($replace), $replace, $content);
    }

    public function getReplacements(string $replaceText): array
    {
        $module = strtolower($this->argument('package'));

        return [
            '{-module}' => strtolower($module),
            '{module}' => Str::snake(str_replace('-', '_', $module)),
            '{+module}' => Str::camel($module),
            '{modules}' => Str::plural(Str::snake(str_replace('-', '_', $module))),
            '{Modules}' => ucfirst(Str::plural(Str::snake(str_replace('-', '_', $module)))),
            '{-modules}' => Str::plural($module),
            '{MODULE}' => strtoupper(Str::snake(str_replace('-', '_', $module))),
            '{Module}' => ucfirst(Str::camel($module)),
        ];
    }

    protected function configure(): void
    {
        $this->addArgument('package', InputArgument::REQUIRED, 'The package name');
        $this->addArgument('name', InputArgument::REQUIRED, 'The CRUD name');
    }
}
