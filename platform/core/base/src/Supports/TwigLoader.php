<?php

namespace Botble\Base\Supports;

use Twig\Loader\LoaderInterface;
use Twig\Source;

class TwigLoader implements LoaderInterface
{
    protected string $content;

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSourceContext(string $name): Source
    {
        return new Source($this->content, $name);
    }

    public function getCacheKey(string $name): string
    {
        return $this->hashedContent();
    }

    public function isFresh(string $name, int $time): bool
    {
        return $name === $this->hashedContent();
    }

    public function exists(string $name): bool
    {
        return true;
    }

    public function hashedContent(): string
    {
        return sha1($this->content);
    }
}
