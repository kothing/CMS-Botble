<?php

namespace Botble\WidgetGenerator\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\Theme\Facades\Theme;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:widget:create', 'Create a new widget')]
class WidgetCreateCommand extends BaseMakeCommand
{
    public function handle(File $files): int
    {
        $widget = $this->getWidget();
        $path = $this->getPath();

        if ($files->isDirectory($path)) {
            $this->components->error('Widget "' . $widget . '" is already exists.');

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $path);
        $this->searchAndReplaceInFiles($widget, $path);
        $this->renameFiles($widget, $path);

        $this->components->info('Widget "' . $widget . '" has been created in ' . $path . '.');

        return self::SUCCESS;
    }

    protected function getWidget(): string
    {
        return strtolower($this->argument('name'));
    }

    protected function getPath(): string
    {
        return theme_path(Theme::getThemeName() . '/widgets/' . $this->getWidget());
    }

    public function getStub(): string
    {
        return __DIR__ . '/../../stubs';
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{widget}' => strtolower($replaceText),
            '{Widget}' => Str::studly($replaceText),
        ];
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The widget name that you want to create');
    }
}
