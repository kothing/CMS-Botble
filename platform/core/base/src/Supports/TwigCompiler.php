<?php

namespace Botble\Base\Supports;

use Twig\Environment;
use Twig\Extension\ExtensionInterface;

class TwigCompiler
{
    protected TwigLoader $loader;

    protected Environment $env;

    public function __construct(array $options = [])
    {
        $this->loader = new TwigLoader();
        $this->env = new Environment($this->loader, $options);

        $this->env->addExtension(new TwigExtension());
    }

    public function compile(string $content, array $data = []): string
    {
        $this->loader->setContent($content);

        return $this->env->render($this->loader->hashedContent(), $data);
    }

    public function addExtension(ExtensionInterface $extension): self
    {
        $this->env->addExtension($extension);

        return $this;
    }

    public function getExtensions(): array
    {
        return $this->env->getExtensions();
    }
}
