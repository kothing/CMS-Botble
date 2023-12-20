<?php

namespace Botble\Shortcode\View;

use ArrayAccess;
use Botble\Shortcode\Compilers\ShortcodeCompiler;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Engine;
use Illuminate\View\View as IlluminateView;

class View extends IlluminateView implements ArrayAccess, Renderable
{
    public ShortcodeCompiler $shortcode;

    public function __construct(Factory $factory, Engine $engine, $view, $path, $data, ShortcodeCompiler $shortcode)
    {
        parent::__construct($factory, $engine, $view, $path, $data);

        $this->shortcode = $shortcode;
    }

    public function withShortcodes(): self
    {
        $this->shortcode->enable();

        return $this;
    }

    public function withoutShortcodes(): self
    {
        $this->shortcode->disable();

        return $this;
    }

    public function withStripShortcodes(): self
    {
        $this->shortcode->setStrip(true);

        return $this;
    }

    protected function renderContents(): string
    {
        // We will keep track of the amount of views being rendered, so we can flush
        // the section after the complete rendering operation is done. This will
        // clear out the sections for any separate views that may be rendered.
        $this->factory->incrementRender();
        $this->factory->callComposer($this);
        $contents = $this->getContents();

        if ($this->shortcode->getStrip()) {
            // strip content without shortcodes
            $contents = $this->shortcode->strip($contents);
        } else {
            // compile the shortcodes
            $contents = $this->shortcode->compile($contents);
        }
        // Once we've finished rendering the view, we'll decrement the render count
        // so that each sections get flushed out next time a view is created and
        // no old sections are staying around in the memory of an environment.
        $this->factory->decrementRender();

        return $contents;
    }
}
