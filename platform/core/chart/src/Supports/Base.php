<?php

namespace Botble\Chart\Supports;

use Botble\Base\Facades\Assets;
use Illuminate\Support\Str;

class Base
{
    /**
     * Type of chart. This value is used in Javascript Morris method
     *
     * @brief Chart
     */
    protected string $chartType = ChartTypes::LINE;

    /**
     * The ID of (or a reference to) the element into which to insert the graph.
     * Note: this element must have a width and height defined in its styling.
     *
     * @brief Element
     */
    protected string $element = '';

    /**
     * The data to plot. This is an array of objects, containing x and y attributes as described by the xkey and ykeys
     * options. Note: the order in which you provide the data is the order in which the bars are displayed.
     *
     * Note 2: if you need to update the plot, use the setData method on the object that Morris.Bar
     * returns (the same as with line charts).
     *
     * @brief Data
     */
    protected array $data = [];

    protected string $hoverCallback;

    protected string $formatter;

    protected string $dateFormat;

    protected array $functions = [
        'hoverCallback',
        'formatter',
        'dateFormat',
    ];

    protected bool $useInlineJs = false;

    /**
     * Create an instance of Morris class
     *
     * @brief Construct
     *
     * @param string $chart Optional. Chart Type of chart. Default ChartTypes::LINE
     *
     * @return void
     */
    public function __construct(string $chart = ChartTypes::LINE)
    {
        $this->chartType = $chart;
        $this->element = $chart . '_' . Str::random(12);
    }

    public function setElementId(string $elementId): Base
    {
        $this->element = $elementId;

        return $this;
    }

    public function getElementId(): string
    {
        return $this->element;
    }

    /**
     * Return the array of this object
     *
     * @brief Array
     */
    public function toArray(): array
    {
        $return = [];
        // @phpstan-ignore-next-line
        foreach ($this as $property => $value) {
            if (str_starts_with($property, '__') || empty($value)) {
                continue;
            }

            if (in_array($property, $this->functions) && str_starts_with($value, 'function')) {
                $value = '%' . $property . '%';
            }

            $return[$property] = $value;
        }

        return $return;
    }

    /**
     * Return the jSON encode of this chart
     *
     * @brief JSON
     */
    public function toJSON(): string
    {
        $json = json_encode($this->toArray());

        return str_replace(
            [
                '"%hoverCallback%"',
                '"%formatter%"',
                '"%dateFormat%"',
            ],
            [
                $this->hoverCallback,
                $this->formatter,
                $this->dateFormat,
            ],
            $json
        );
    }

    public function __get(string $name)
    {
        // @phpstan-ignore-next-line
        foreach ($this as $key => $value) {
            if ($name == $key) {
                return $this->{$key};
            }
        }

        $method = 'get' . ucfirst($name) . 'Attribute';

        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }

        return null;
    }

    public function __call(string $name, array $arguments)
    {
        // @phpstan-ignore-next-line
        foreach ($this as $key => $value) {
            if ($name == $key) {
                $this->{$key} = $arguments[0];

                return $this;
            }
        }

        return false;
    }

    public function renderChart(): string
    {
        Assets::addStyles(['morris'])
            ->addScripts(['morris', 'raphael']);

        $chart = $this;

        return view('core/chart::chart', compact('chart'))->render();
    }

    public function init(): Base
    {
        return $this;
    }

    public function isUseInlineJs(): bool
    {
        return $this->useInlineJs;
    }

    public function setUseInlineJs(bool $useInlineJs): self
    {
        $this->useInlineJs = $useInlineJs;

        return $this;
    }
}
