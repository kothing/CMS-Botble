<?php

namespace Botble\Base\Supports;

use Botble\Assets\Assets as BaseAssets;
use Botble\Assets\HtmlBuilder;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * @since 22/07/2015 11:23 PM
 */
class Assets extends BaseAssets
{
    public function __construct(Repository $config, HtmlBuilder $htmlBuilder)
    {
        parent::__construct($config, $htmlBuilder);

        $this->config = $config->get('core.base.assets');

        $this->scripts = $this->config['scripts'];

        $this->styles = $this->config['styles'];
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getThemes(): array
    {
        $themeFolder = '/vendor/core/core/base/css/themes';

        $themes = ['default' => $themeFolder . '/default.css'];

        if (! File::isDirectory(public_path($themeFolder))) {
            return $themes;
        }

        $files = File::files(public_path($themeFolder));

        if (empty($files)) {
            return $themes;
        }

        foreach ($files as $file) {
            $name = $themeFolder . '/' . basename($file);
            if (! Str::contains($file, '.css.map')) {
                $themes[basename($file, '.css')] = $name;
            }
        }

        return $themes;
    }

    public function renderHeader($lastStyles = []): string
    {
        do_action(BASE_ACTION_ENQUEUE_SCRIPTS);

        return parent::renderHeader($lastStyles);
    }

    public function renderFooter(): string
    {
        $bodyScripts = $this->getScripts(self::ASSETS_SCRIPT_POSITION_FOOTER);

        return view('assets::footer', compact('bodyScripts'))->render();
    }

    public function usingVueJS(): self
    {
        $this->addScripts(['vue', 'vue-app']);

        return $this;
    }

    public function disableVueJS(): self
    {
        $this->removeScripts(['vue', 'vue-app']);

        return $this;
    }
}
