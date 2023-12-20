<?php

namespace Botble\PluginGenerator\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:plugin:create', 'Create a plugin in the /platform/plugins directory.')]
class PluginCreateCommand extends BaseMakeCommand
{
    public function handle(): int
    {
        if (! preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $plugin = strtolower($this->argument('name'));
        $location = plugin_path($plugin);

        if (File::isDirectory($location)) {
            $this->components->error('A plugin named [' . $plugin . '] already exists.');

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $location);
        File::copy(__DIR__ . '/../../stubs/plugin.json', $location . '/plugin.json');
        File::copy(__DIR__ . '/../../stubs/Plugin.stub', $location . '/src/Plugin.php');
        $this->renameFiles($plugin, $location);
        $this->searchAndReplaceInFiles($plugin, $location);
        $this->removeUnusedFiles($location);
        $this->line('------------------');
        $this->line(
            '<info>The plugin</info> <comment>' . $plugin . '</comment> <info>was created in</info> <comment>' . $location . '</comment><info>, customize it!</info>'
        );
        $this->line('------------------');
        $this->call('cache:clear');

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return package_path('dev-tool/stubs/module');
    }

    protected function removeUnusedFiles(string $location)
    {
        File::delete($location . '/composer.json');
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{type}' => 'plugin',
            '{types}' => 'plugins',
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
        $this->addArgument('name', InputArgument::REQUIRED, 'The plugin name that you want to create');
    }
}
