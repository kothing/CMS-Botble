<?php

namespace Botble\Chart;

use Botble\Chart\Supports\Chart;
use Botble\Chart\Supports\ChartTypes;

class BarChart extends Chart
{
    protected float $barSizeRatio = 0.75;

    protected int $barGap = 3;

    protected float $barOpacity = 1.0;

    protected array $barRadius = [0, 0, 0, 0];

    protected int $xLabelMargin = 50;

    protected array $barColors = [
        '#0b62a4',
        '#7a92a3',
        '#4da74d',
        '#afd8f8',
        '#edc240',
        '#cb4b4b',
        '#9440ed',
    ];

    /**
     * Set to true to draw bars stacked vertically.
     *
     * @brief Stacked
     */
    protected bool $stacked = true;

    /**
     * Create an instance of MorrisBarCharts class
     */
    public function __construct()
    {
        parent::__construct(ChartTypes::BAR);
    }
}
