<?php

namespace Botble\Chart\Supports;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class ChartBuilder
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function create(string $chartClass): Chart
    {
        if (! class_exists($chartClass)) {
            throw new InvalidArgumentException(
                'Chart class with name ' . $chartClass . ' does not exist.'
            );
        }

        return $this->container->make($chartClass);
    }
}
