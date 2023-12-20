<?php

namespace Botble\Widget;

use Botble\Theme\Facades\Theme;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use ReflectionClass;

abstract class AbstractWidget
{
    private array $config = [];

    private array $extraAdminConfig = [];

    private string $frontendTemplate = 'frontend';

    private string $backendTemplate = 'backend';

    protected string|null $theme = null;

    protected array|Collection $data = [];

    protected bool $loaded = false;

    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            $this->config[$key] = $value;
        }
    }

    public function getWidgetDirectory(): string|null
    {
        $reflection = new ReflectionClass($this);

        return File::basename(File::dirname($reflection->getFilename()));
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    protected function adminConfig(): array
    {
        return $this->extraAdminConfig;
    }

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run(): string|null
    {
        $widgetGroup = app('botble.widget-group-collection');
        $widgetGroup->load();
        $widgetGroupData = $widgetGroup->getData();

        Theme::uses(Theme::getThemeName());

        $args = func_get_args();
        $data = $widgetGroupData
            ->where('widget_id', $this->getId())
            ->where('sidebar_id', $args[0])
            ->where('position', $args[1])
            ->first();

        if (! empty($data)) {
            $this->config = array_merge($this->config, $data->data);
        }

        $viewData = array_merge([
            'config' => $this->config,
            'sidebar' => $args[0],
        ], $this->data());

        $html = null;

        $widgetDirectory = $this->getWidgetDirectory();

        if (View::exists(Theme::getThemeNamespace('widgets.' . $widgetDirectory . '.templates.' . $this->frontendTemplate))) {
            $html = Theme::loadPartial(
                $this->frontendTemplate,
                Theme::getThemeNamespace('/../widgets/' . $widgetDirectory . '/templates'),
                $viewData
            );
        } elseif (view()->exists($this->frontendTemplate)) {
            $html = view($this->frontendTemplate, $viewData)->render();
        }

        return apply_filters('widget_rendered', $html, $this);
    }

    public function getId(): string
    {
        return get_class($this);
    }

    public function form(string|null $sidebarId = null, int $position = 0): string|null
    {
        Theme::uses(Theme::getThemeName());

        if (! empty($sidebarId)) {
            $widgetGroup = app('botble.widget-group-collection');
            $widgetGroup->load();
            $widgetGroupData = $widgetGroup->getData();

            $data = $widgetGroupData
                ->where('widget_id', $this->getId())
                ->where('sidebar_id', $sidebarId)
                ->where('position', $position)
                ->first();

            if (! empty($data)) {
                $this->config = array_merge($this->config, $data->data);
            }
        }

        $widgetDirectory = $this->getWidgetDirectory();

        if (View::exists(Theme::getThemeNamespace('widgets.' . $widgetDirectory . '.templates.' . $this->backendTemplate))) {
            return Theme::loadPartial(
                $this->backendTemplate,
                Theme::getThemeNamespace('/../widgets/' . $widgetDirectory . '/templates'),
                array_merge([
                    'config' => $this->config,
                ], $this->adminConfig())
            );
        }

        if (! view()->exists($this->backendTemplate)) {
            return null;
        }

        return view($this->backendTemplate, array_merge([
            'config' => $this->config,
        ], $this->adminConfig()))->render();
    }

    protected function data(): array|Collection
    {
        return $this->data;
    }

    protected function setBackendTemplate(string $template): self
    {
        $this->backendTemplate = $template;

        return $this;
    }

    protected function setFrontendTemplate(string $template): self
    {
        $this->frontendTemplate = $template;

        return $this;
    }
}
