<?php

namespace Botble\Optimize\Facades;

use Botble\Optimize\Supports\Optimizer;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isEnabled()
 * @method static \Botble\Optimize\Supports\Optimizer enable()
 * @method static \Botble\Optimize\Supports\Optimizer disable()
 *
 * @see \Botble\Optimize\Supports\Optimizer
 */
class OptimizerHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Optimizer::class;
    }
}
