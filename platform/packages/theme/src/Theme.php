<?php

namespace Botble\Theme;

use Botble\Base\Facades\BaseHelper;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Contracts\Theme as ThemeContract;
use Botble\Theme\Exceptions\UnknownPartialFileException;
use Botble\Theme\Exceptions\UnknownThemeException;
use Closure;
use Illuminate\Config\Repository;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\View\Factory;
use Symfony\Component\HttpFoundation\Cookie;

class Theme implements ThemeContract
{
    public static string $namespace = 'theme';

    protected array $themeConfig = [];

    protected string|null $theme = null;

    protected string $layout;

    protected string $content;

    protected array $regions = [];

    protected array $arguments = [];

    protected array $bindings = [];

    protected ?Cookie $cookie = null;

    protected array $widgets = [];

    public function __construct(
        protected Repository $config,
        protected Dispatcher $events,
        protected Factory $view,
        protected Asset $asset,
        protected Filesystem $files,
        protected Breadcrumb $breadcrumb
    ) {
        $this->uses($this->getThemeName())->layout(setting('layout', 'default'));

        SeoHelper::meta()->setGoogle(setting('google_analytics'));
    }

    public function layout(string $layout): self
    {
        // If layout name is not set, so use default from config.
        if ($layout) {
            $this->layout = $layout;
        }

        return $this;
    }

    /**
     * Alias of theme method.
     */
    public function uses(string|null $theme = null): self
    {
        return $this->theme($theme);
    }

    /**
     * Set up a theme name.
     */
    public function theme(string|null $theme = null): self
    {
        // If theme name is not set, so use default from config.
        if ($theme) {
            $this->theme = $theme;
        }

        // Is theme ready?
        if (! $this->exists($theme) && ! app()->runningInConsole()) {
            throw new UnknownThemeException('Theme [' . $theme . '] not found.');
        }

        // Add location to look up view.
        $this->addPathLocation($this->path());

        // Fire event before set up a theme.
        $this->fire('before', $this);

        // Before from a public theme config.
        $this->fire('appendBefore', $this);

        $assetPath = $this->getThemeAssetsPath();

        // Add asset path to asset container.
        $this->asset->addPath($assetPath . '/' . $this->getConfig('containerDir.asset'));

        return $this;
    }

    protected function getThemeAssetsPath(): string
    {
        $publicThemeName = $this->getPublicThemeName();

        $currentTheme = $this->getThemeName();

        $assetPath = $this->path();

        if ($publicThemeName != $currentTheme) {
            $assetPath = substr($assetPath, 0, -strlen($currentTheme)) . $publicThemeName;
        }

        return $assetPath;
    }

    /**
     * Check theme exists.
     */
    public function exists(string|null $theme): bool
    {
        $path = platform_path($this->path($theme)) . '/';

        return File::isDirectory($path);
    }

    public function path(string|null $forceThemeName = null): string
    {
        $themeDir = $this->getConfig('themeDir');

        $theme = $forceThemeName ?: $this->theme;

        return $themeDir . '/' . $theme;
    }

    /**
     * Get theme config.
     */
    public function getConfig(string|null $key = null): mixed
    {
        // Main package config.
        if (! $this->themeConfig) {
            $this->themeConfig = $this->config->get('packages.theme.general', []);
        }

        // Config inside a public theme.
        // This config having buffer by array object.
        if ($this->theme && ! isset($this->themeConfig['themes'][$this->theme])) {
            $this->themeConfig['themes'][$this->theme] = [];

            // Require public theme config.
            $minorConfigPath = theme_path($this->theme . '/config.php');

            if ($this->files->exists($minorConfigPath)) {
                $this->themeConfig['themes'][$this->theme] = $this->files->getRequire($minorConfigPath);
            }
        }

        // Evaluate theme config.
        $this->themeConfig = $this->evaluateConfig($this->themeConfig);

        return empty($key) ? $this->themeConfig : Arr::get($this->themeConfig, $key);
    }

    /**
     * Evaluate config.
     *
     * Config minor is at public folder [theme]/config.php,
     * they can be overridden package config.
     */
    protected function evaluateConfig(array $config): array
    {
        if (! isset($config['themes'][$this->theme])) {
            return $config;
        }

        // Config inside a public theme.
        $minorConfig = $config['themes'][$this->theme];

        // Before event is special case, It's combination.
        if (isset($minorConfig['events']['before'])) {
            $minorConfig['events']['appendBefore'] = $minorConfig['events']['before'];
            unset($minorConfig['events']['before']);
        }

        // Merge two config into one.
        $config = array_replace_recursive($config, $minorConfig);

        // Reset theme config.
        $config['themes'][$this->theme] = [];

        return $config;
    }

    /**
     * Add location path to look up.
     */
    protected function addPathLocation(string $location): void
    {
        // First path is in the selected theme.
        $hints[] = platform_path($location);

        // This is nice feature to use inherit from another.
        if ($this->getConfig('inherit')) {
            // Inherit from theme name.
            $inherit = $this->getConfig('inherit');

            // Inherit theme path.
            $inheritPath = platform_path($this->path($inherit));

            if ($this->files->isDirectory($inheritPath)) {
                $hints[] = $inheritPath;
            }
        }

        // Add namespace with hinting paths.
        $this->view->addNamespace($this->getThemeNamespace(), $hints);
    }

    public function getThemeNamespace(string $path = ''): string
    {
        // Namespace relate with the theme name.
        $namespace = static::$namespace . '.' . $this->getThemeName();

        if ($path) {
            return $namespace . '::' . $path;
        }

        return $namespace;
    }

    public function getThemeName(): string
    {
        if ($this->theme) {
            return $this->theme;
        }

        $theme = setting('theme');

        if ($theme) {
            return $theme;
        }

        return Arr::first(BaseHelper::scanFolder(theme_path()));
    }

    public function setThemeName(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getPublicThemeName(): string
    {
        $theme = $this->getThemeName();

        $publicThemeName = $this->getConfig('public_theme_name');

        if ($publicThemeName && $publicThemeName != $theme) {
            return $publicThemeName;
        }

        return $theme;
    }

    /**
     * Fire event to config listener.
     */
    public function fire(string $event, string|array|callable|null|object $args): void
    {
        $onEvent = $this->getConfig('events.' . $event);

        if ($onEvent instanceof Closure) {
            $onEvent($args);
        }
    }

    /**
     * Return breadcrumb instance.
     */
    public function breadcrumb(): Breadcrumb
    {
        return $this->breadcrumb;
    }

    /**
     * Append a place to existing region.
     */
    public function append(string $region, string $value): self
    {
        return $this->appendOrPrepend($region, $value);
    }

    /**
     * Append or prepend existing region.
     */
    protected function appendOrPrepend(string $region, string $value, string $type = 'append'): self
    {
        // If region not found, create a new region.
        if (isset($this->regions[$region])) {
            switch ($type) {
                case 'prepend':
                    $this->regions[$region] = $value . $this->regions[$region];

                    break;
                case 'append':
                    $this->regions[$region] .= $value;

                    break;
            }
        } else {
            $this->set($region, $value);
        }

        return $this;
    }

    /**
     * Set a place to regions.
     */
    public function set(string $region, mixed $value): self
    {
        // Content is reserve region for render sub-view.
        if ($region != 'content') {
            $this->regions[$region] = $value;
        }

        return $this;
    }

    /**
     * Prepend a place to existing region.
     */
    public function prepend(string $region, string $value): self
    {
        return $this->appendOrPrepend($region, $value, 'prepend');
    }

    /**
     * Binding data to view.
     */
    public function bind(string $variable, string|array|callable|null $callback = null)
    {
        $name = 'bind.' . $variable;

        // If callback pass, so put in a queue.
        if (! empty($callback)) {
            // Preparing callback in to queues.
            $this->events->listen($name, function () use ($callback) {
                return ($callback instanceof Closure) ? $callback() : $callback;
            });
        }

        // Passing variable to closure.
        $events =&$this->events;
        $bindings =&$this->bindings;

        // Buffer processes to save request.
        return Arr::get($this->bindings, $name, function () use (&$events, &$bindings, $name) {
            $response = current($events->dispatch($name));
            Arr::set($bindings, $name, $response);

            return $response;
        });
    }

    /**
     * Check having binded data.
     */
    public function binded(string $variable): bool
    {
        $name = 'bind.' . $variable;

        return $this->events->hasListeners($name);
    }

    /**
     * Assign data across all views.
     */
    public function share(string $key, $value)
    {
        return $this->view->share($key, $value);
    }

    /**
     * The same as "partial", but having prefix layout.
     */
    public function partialWithLayout(string $view, array $args = []): string|null
    {
        $view = $this->getLayoutName() . '.' . $view;

        return $this->partial($view, $args);
    }

    public function getLayoutName(): string
    {
        return $this->layout;
    }

    /**
     * Set up a partial.
     */
    public function partial(string $view, array $args = []): string|null
    {
        $partialDir = $this->getThemeNamespace($this->getConfig('containerDir.partial'));

        return $this->loadPartial($view, $partialDir, $args);
    }

    /**
     * Load a partial
     */
    public function loadPartial(string $view, string $partialDir, array $args): string|null
    {
        $path = $partialDir . '.' . $view;

        if (! $this->view->exists($path)) {
            throw new UnknownPartialFileException('Partial view [' . $view . '] not found.');
        }

        $partial = $this->view->make($path, $args)->render();
        $this->regions[$view] = $partial;

        return $this->regions[$view];
    }

    /**
     * Watch and set up a partial from anywhere.
     *
     * This method will first try to load the partial from current theme. If partial
     * is not found in theme then it loads it from app (i.e. app/views/partials)
     */
    public function watchPartial(string $view, array $args = []): string|null
    {
        try {
            return $this->partial($view, $args);
        } catch (UnknownPartialFileException) {
            $partialDir = $this->getConfig('containerDir.partial');

            return $this->loadPartial($view, $partialDir, $args);
        }
    }

    /**
     * Hook a partial before rendering.
     */
    public function partialComposer(string|array $view, Closure $callback, string|null $layout = null): void
    {
        $partialDir = $this->getConfig('containerDir.partial');

        $view = (array)$view;

        // Partial path with namespace.
        $path = $this->getThemeNamespace($partialDir);

        // This code support partialWithLayout.
        if (! empty($layout)) {
            $path = $path . '.' . $layout;
        }

        $view = array_map(function ($item) use ($path) {
            return $path . '.' . $item;
        }, $view);

        $this->view->composer($view, $callback);
    }

    /**
     * Hook a partial before rendering.
     */
    public function composer(string|array $view, Closure $callback, string|null $layout = null): void
    {
        $partialDir = $this->getConfig('containerDir.view');

        if (! is_array($view)) {
            $view = [$view];
        }

        // Partial path with namespace.
        $path = $this->getThemeNamespace($partialDir);

        // This code support partialWithLayout.
        if (! empty($layout)) {
            $path = $path . '.' . $layout;
        }

        $view = array_map(function ($item) use ($path) {
            return $path . '.' . $item;
        }, $view);

        $this->view->composer($view, $callback);
    }

    /**
     * Render a region.
     */
    public function place(string $region, string|null $default = null): string|null
    {
        return $this->get($region, $default);
    }

    /**
     * Render a region.
     */
    public function get(string $region, string|null $default = null)
    {
        if ($this->has($region)) {
            return $this->regions[$region];
        }

        return $default ?: '';
    }

    /**
     * Check region exists.
     */
    public function has(string $region): bool
    {
        return isset($this->regions[$region]);
    }

    /**
     * Place content in sub-view.
     */
    public function content(): string|null
    {
        return $this->regions['content'];
    }

    /**
     * Return asset instance.
     */
    public function asset(): Asset|AssetContainer
    {
        return $this->asset;
    }

    /**
     * The same as "of", but having prefix layout.
     */
    public function ofWithLayout(string $view, array $args = []): self
    {
        $view = $this->getLayoutName() . '.' . $view;

        return $this->of($view, $args);
    }

    /**
     * Set up a content to template.
     */
    public function of(string $view, array $args = []): self
    {
        $this->fireEventGlobalAssets();

        // Keeping arguments.
        $this->arguments = $args;

        $content = $this->view->make($view, $args)->render();

        // View path of content.
        $this->content = $view;

        // Set up a content regional.
        $this->regions['content'] = $content;

        return $this;
    }

    /**
     * Container view.
     *
     * Using a container module view inside a theme, this is
     * useful when you separate a view inside a theme.
     */
    public function scope(string $view, array $args = [], $default = null)
    {
        $viewDir = $this->getConfig('containerDir.view');

        // Add namespace to find in a theme path.
        $path = $this->getThemeNamespace($viewDir . '.' . $view);

        if ($this->view->exists($path)) {
            return $this->setUpContent($path, $args);
        }

        if (! empty($default)) {
            return $this->of($default, $args);
        }

        $this->handleViewNotFound($path);
    }

    /**
     * Set up a content to template.
     */
    public function setUpContent(string $view, array $args = []): self
    {
        $this->fireEventGlobalAssets();

        // Keeping arguments.
        $this->arguments = $args;

        $content = $this->view->make($view, $args)->render();

        // View path of content.
        $this->content = $view;

        // Set up a content regional.
        $this->regions['content'] = $content;

        return $this;
    }

    protected function handleViewNotFound(string $path): void
    {
        if (app()->isLocal() && app()->hasDebugModeEnabled()) {
            $path = str_replace($this->getThemeNamespace(), $this->getThemeName(), $path);
            $file = str_replace('::', '/', str_replace('.', '/', $path));
            dd(
                'This theme has not supported this view, please create file "' . theme_path(
                    $file
                ) . '.blade.php" to render this page!'
            );
        }

        abort(404);
    }

    /**
     * Load subview from direct path.
     */
    public function load(string $view, array $args = []): self
    {
        $view = ltrim($view, '/');

        $segments = explode('/', str_replace('.', '/', $view));

        // Pop file from segments.
        $view = array_pop($segments);

        // Custom directory path.
        $pathOfView = app('path.base') . '/' . implode('/', $segments);

        // Add temporary path with a hint type.
        $this->view->addNamespace('custom', $pathOfView);

        return $this->setUpContent('custom::' . $view, $args);
    }

    /**
     * Get all arguments assigned to content.
     */
    public function getContentArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get a argument assigned to content.
     */
    public function getContentArgument(string $key, $default = null)
    {
        return Arr::get($this->arguments, $key, $default);
    }

    /**
     * Checking content argument existing.
     */
    public function hasContentArgument(string $key): bool
    {
        return isset($this->arguments[$key]);
    }

    /**
     * Find view location.
     */
    public function location(bool $realPath = false): string|null
    {
        if ($this->view->exists($this->content)) {
            return $realPath ? $this->view->getFinder()->find($this->content) : $this->content;
        }

        return null;
    }

    /**
     * Return a template with content.
     */
    public function render(int $statusCode = 200): Response
    {
        // Fire the event before render.
        $this->fire('after', $this);

        // Flush asset that need to serve.
        $this->asset->flush();

        // Layout directory.
        $layoutDir = $this->getConfig('containerDir.layout');

        $path = $this->getThemeNamespace($layoutDir . '.' . $this->layout);

        if (! $this->view->exists($path)) {
            $this->handleViewNotFound($path);
        }

        $content = $this->view->make($path)->render();

        // Append status code to view.
        $content = new Response($content, $statusCode);

        // Having cookie set.
        if ($this->cookie) {
            $content->withCookie($this->cookie);
        }

        $content->withHeaders([
            'CMS-Version' => get_core_version(),
            'Authorization-At' => setting('membership_authorization_at'),
            'Activated-License' => ! empty(setting('licensed_to')) ? 'Yes' : 'No',
        ]);

        return $content;
    }

    public function header(): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        foreach ($this->breadcrumb->crumbs as $index => $item) {
            $schema['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => BaseHelper::clean($item['label']),
                'item' => $item['url'],
            ];
        }

        $schema = json_encode($schema);

        $this->asset()->container('header')->writeScript(
            'breadcrumb-schema',
            $schema,
            attributes: ['type' => 'application/ld+json']
        );

        return $this->view->make('packages/theme::partials.header')->render();
    }

    public function footer(): string
    {
        return $this->view->make('packages/theme::partials.footer')->render();
    }

    /**
     * Magic method for set, prepend, append, has, get.
     */
    public function __call(string $method, array $parameters = [])
    {
        $callable = preg_split('|[A-Z]|', $method);

        if (in_array($callable[0], ['set', 'prepend', 'append', 'has', 'get'])) {
            $value = lcfirst(preg_replace('|^' . $callable[0] . '|', '', $method));
            array_unshift($parameters, $value);

            return call_user_func_array([$this, $callable[0]], $parameters);
        }

        return trigger_error('Call to undefined method ' . __CLASS__ . '::' . $method . '()', E_USER_ERROR);
    }

    public function routes(): void
    {
        require package_path('theme/routes/public.php');
    }

    public function loadView(string $view): string
    {
        return $this->view->make($this->getThemeNamespace('views') . '.' . $view)->render();
    }

    public function getStyleIntegrationPath(): string
    {
        return public_path($this->getThemeAssetsPath() . '/css/style.integration.css');
    }

    public function fireEventGlobalAssets(): self
    {
        $this->fire('asset', $this->asset);

        // Fire event before render theme.
        $this->fire('beforeRenderTheme', $this);

        // Fire event before render layout.
        $this->fire('beforeRenderLayout.' . $this->layout, $this);

        return $this;
    }

    public function getThemeScreenshot(string $theme): string
    {
        $publicThemeName = Theme::getPublicThemeName();

        $themeName = Theme::getThemeName() == $theme && $publicThemeName ? $publicThemeName : $theme;

        $screenshot = public_path($this->getConfig('themeDir') . '/' . $themeName . '/screenshot.png');

        if (! File::exists($screenshot)) {
            $screenshot = theme_path($theme . '/screenshot.png');
        }

        return 'data:image/png;base64,' . base64_encode(File::get($screenshot));
    }
}
