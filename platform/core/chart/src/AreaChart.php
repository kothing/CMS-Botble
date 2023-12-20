<?php

namespace Botble\Chart;

use Botble\Chart\Supports\Chart;
use Botble\Chart\Supports\ChartTypes;

class AreaChart extends Chart
{
    /**
     * Change the opacity of the area fill colour.
     * Accept values between 0.0 (for completely transparent) and 1.0 (for completely opaque).
     *
     * @brief Opacity
     */
    protected string $fillOpacity = 'auto';

    /**
     * Set to true to overlay the areas on top of each other instead of stacking them.
     *
     * @brief Line
     */
    protected bool $behaveLikeLine = false;

    protected array $pointFillColors = [];

    protected array $pointStrokeColors = [];

    protected array $lineColors = [];

    public function __construct()
    {
        parent::__construct(ChartTypes::AREA);
    }
}
