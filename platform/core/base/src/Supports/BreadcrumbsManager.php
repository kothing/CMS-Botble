<?php

namespace Botble\Base\Supports;

use Exception;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Traits\Macroable;
use stdClass;

class BreadcrumbsManager
{
    use Macroable;

    protected array $callbacks = [];

    protected array $before = [];

    protected array $after = [];

    protected array|null $route;

    public function __construct(
        protected BreadcrumbsGenerator $generator,
        protected Router $router,
        protected ViewFactory $viewFactory
    ) {
    }

    public function register(string $name, callable $callback): void
    {
        $this->for($name, $callback);
    }

    public function for(string $name, callable $callback): void
    {
        if (! isset($this->callbacks[$name])) {
            $this->callbacks[$name] = $callback;
        }
    }

    public function before(callable $callback): void
    {
        $this->before[] = $callback;
    }

    public function after(callable $callback): void
    {
        $this->after[] = $callback;
    }

    public function exists(string $name = null): bool
    {
        if (empty($name)) {
            try {
                [$name] = $this->getCurrentRoute();
            } catch (Exception) {
                return false;
            }
        }

        return isset($this->callbacks[$name]);
    }

    protected function getCurrentRoute(): array|null
    {
        // Manually set route
        if ($this->route) {
            return $this->route;
        }

        // Determine the current route
        $route = $this->router->current();

        // No current route - must be the 404 page
        if ($route === null) {
            return ['errors.404', []];
        }

        // Convert route to name
        $name = $route->getName();

        if ($name === null) {
            return ['errors.404', []];
        }

        $params = array_values($route->parameters());

        return [$name, $params];
    }

    public function render(string $name = null, ...$params): string
    {
        return $this->view('core/base::layouts.partials.breadcrumbs', $name, ...$params)->toHtml();
    }

    public function view(string $view, string $name = null, ...$params): HtmlString
    {
        $breadcrumbs = $this->generate($name, ...$params);

        $html = $this->viewFactory->make($view, compact('breadcrumbs'))->render();

        return new HtmlString($html);
    }

    public function generate(string $name = null, ...$params): Collection
    {
        // Route-bound breadcrumbs
        if ($name === null) {
            try {
                [$name, $params] = $this->getCurrentRoute();
            } catch (Exception) {
                return new Collection();
            }
        }

        try {
            return $this->generator->generate($this->callbacks, $this->before, $this->after, $name, $params);
        } catch (Exception) {
            return new Collection();
        }
    }

    public function current(): ?stdClass
    {
        return $this->generate()->where('current', '!==', false)->last();
    }

    public function setCurrentRoute(string $name, ...$params): void
    {
        $this->route = [$name, $params];
    }

    public function clearCurrentRoute(): void
    {
        $this->route = null;
    }
}
