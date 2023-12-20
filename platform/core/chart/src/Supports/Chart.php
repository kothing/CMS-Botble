<?php

namespace Botble\Chart\Supports;

class Chart extends Base
{
    /**
     * A string containing the name of the attribute that contains date (X) values.
     * Timestamps are accepted in the form of millisecond timestamps (as returned by Date.getTime() or as strings in the
     * following formats:
     * 2012
     * 2012 Q1
     * 2012 W1
     * 2012-02
     * 2012-02-24     *  15:00
     * 2012-02-24 15:00:00
     * 2012-02-24 15:00:00.000
     *
     * Note: when using millisecond timestamps, it's recommended that you check out the dateFormat option.
     * Note 2: date/time strings can optionally contain a T between the date and time parts, and/or a Z suffix, for
     * compatibility with ISO-8601 dates.
     *
     * @brief XKeys
     */
    protected array $xkey = [];

    /**
     * A list of strings containing names of attributes that contain Y values (one for each series of data to be plotted).
     *
     * @brief YKeys
     */
    protected array $ykeys = [];

    /**
     * A list of strings containing labels for the data series to be plotted (corresponding to the values in the ykeys
     * option).
     *
     * @brief Labels
     */
    protected array $labels = [];

    /**
     * Max. bound for Y-values. Alternatively, set this to 'auto' to compute automatically, or 'auto [num]' to
     * automatically compute and ensure that the max y-value is at least [num].
     *
     * @brief Max Y
     */
    protected string $ymax = 'auto';

    /**
     * Min. bound for Y-values. Alternatively, set this to 'auto' to compute automatically, or 'auto [num]' to
     * automatically compute and ensure that the min y-value is at most [num].
     * Hint: you can use this to create graphs with false origins.
     *
     * @brief Min Y
     */
    protected int $ymin = 0;

    /**
     * Set too false to always show a hover legend.
     * Set to true or 'auto' to only show the hover legend when the mouse cursor is over the chart.
     * Set to 'always' to never show a hover legend.
     *
     * @brief Hide over legend
     */
    protected string $hideHover = 'auto';

    /**
     * Provide a function on this option to generate custom hover legends. The function will be called with the index of
     * the row under the hover legend, the options object passed to the constructor as arguments, and a string containing
     * the default generated hover legend content HTML.
     * eg:
     *
     *     hoverCallback: function (index, options, content) {
     *       var row = options.data[index];
     *       return "sin(" + row.x + ") = " + row.y;
     *     }
     *
     *
     * @brief Hover callback
     */
    protected string $hoverCallback = '';

    /**
     * Set too false to disable drawing the x and y axes.
     *
     * @brief Axes
     */
    protected bool $axes = true;

    /**
     * Set too false to disable drawing the horizontal grid lines.
     *
     * @brief Grid
     */
    protected bool $grid = true;

    /**
     * Set the color of the axis labels (default: #888).
     *
     * @brief Grid text color
     */
    protected string $gridTextColor = '#888';

    /**
     * Set the point size of the axis labels (default: 12).
     *
     * @brief Grid text color
     */
    protected string $gridTextSize = '12';

    /**
     * Set the font family of the axis labels (default: sans-serif).
     *
     * @brief Grid font
     */
    protected string $gridTextFamily = 'sans-serif';

    /**
     * Set the font weight of the axis labels (default: normal).
     *
     * @brief Font weight
     */
    protected string $gridTextWeight = 'normal';

    /**
     * Set to true to enable automatic resizing when the containing element resizes. (default: false).
     * This has a significant performance impact, so is disabled by default.
     *
     * @brief Resize
     *
     * @since Morris 0.5.0
     */
    protected bool $resize = false;

    protected string|null $rangeSelect = null;

    protected string $rangeSelectColor = '#eef';

    protected int $padding = 25;

    protected int $numLines = 5;

    /**
     * A list of x-values to draw as vertical 'event' lines on the chart.
     *
     * eg: events: ['2012-01-01', '2012-02-01', '2012-03-01']
     *
     * @brief Events
     */
    protected array $events = [];

    /**
     * Width, in pixels, of the event lines.
     *
     * @brief Events line width
     */
    protected int $eventStrokeWidth = 1;

    /**
     * Array of color values to use for the event line colors. If you list fewer colors here than you have lines in
     * events, then the values will be cycled.
     *
     * @brief Events colors
     */
    protected array $eventLineColors = ['#005a04', '#ccffbb', '#3a5f0b', '#005502'];

    /**
     * A list of y-values to draw as horizontal 'goal' lines on the chart.
     *
     * eg: goals: [1.0, -1.0]
     *
     * @brief Goals
     */
    protected array $goals = [];

    /**
     * Width, in pixels, of the goal lines.
     *
     * @brief Goal stroke width
     */
    protected int $goalStrokeWidth = 1;

    /**
     * Array of color values to use for the goal line colors. If you list fewer colors here than you have lines in goals,
     * then the values will be cycled.
     *
     * @brief Goals colors
     */
    protected array $goalLineColors = ['#666633', '#999966', '#cc6666', '#663333'];

    /**
     * Set too false to skip time/date parsing for X values, instead treating them as an equally-spaced series.
     *
     * @brief Parse time
     */
    protected bool $parseTime = true;

    /**
     * Set to a string value (eg: '%') to add a label suffix all y-labels.
     *
     * @brief Post units
     */
    protected string $postUnits = '';

    /**
     * Set to a string value (eg: '$') to add a label prefix all y-labels.
     *
     * @brief Pre units
     */
    protected string $preUnits = '';

    /**
     * Angle of x label
     *
     * @brief Angle
     */
    protected int $xLabelAngle = 0;
}
