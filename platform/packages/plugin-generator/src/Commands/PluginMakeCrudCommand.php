<?php

namespace Botble\PluginGenerator\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:plugin:make:crud', 'Create a CRUD inside a plugin')]
class PluginMakeCrudCommand extends BaseMakeCommand
{
    public function handle(): int
    {
        if (! preg_match('/^[a-z0-9\-]+$/i', $this->argument('plugin')) || ! preg_match(
            '/^[a-z0-9\-]+$/i',
            $this->argument('name')
        )) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $plugin = strtolower($this->argument('plugin'));
        $location = plugin_path($plugin);

        if (! File::isDirectory($location)) {
            $this->components->error('Plugin named [' . $plugin . '] does not exists.');

            return self::FAILURE;
        }

        $name = strtolower($this->argument('name'));

        $this->publishStubs($this->getStub(), $location);
        $this->removeUnusedFiles($location);
        $this->renameFiles($name, $location);
        $this->searchAndReplaceInFiles($name, $location);
        $this->line('------------------');
        $this->line(
            '<info>The CRUD for plugin </info> <comment>' . $plugin . '</comment> <info>was created in</info> <comment>' . $location . '</comment><info>, customize it!</info>'
        );
        $this->line('------------------');
        $this->call('cache:clear');

        $replacements = [
            'config/permissions.stub',
            'helpers/constants.stub',
            'routes/web.stub',
            'src/Providers/{Module}ServiceProvider.stub',
            'src/Plugin.stub',
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
            File::delete($location . '/' . $file);
        }
    }

    protected function replacementSubModule(string $file = null, $content = null): string
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
        $module = strtolower($this->argument('plugin'));

        return [
            '{type}' => 'plugin',
            '{types}' => 'plugins',
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
        $this->addArgument('plugin', InputArgument::REQUIRED, 'The plugin name');
        $this->addArgument('name', InputArgument::REQUIRED, 'The CRUD name');
    }
}
