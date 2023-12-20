<?php

namespace Botble\Base\Widgets;

abstract class Chart extends Widget
{
    protected string $view = 'chart';

    protected string $type = 'line';

    protected int $height = 275;

    protected string $strokeCurve = 'smooth';

    public function options(): array
    {
        return [
            'stroke' => [
                'curve' => $this->strokeCurve,
            ],
            'chart' => [
                'height' => $this->height,
                'toolbar' => [
                    'show' => false,
                ],
                'type' => $this->type,
            ],
            'series' => [],
            'xaxis' => [
                'categories' => [],
            ],
        ];
    }

    abstract public function getOptions(): array;

    public function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'options' => array_merge($this->options(), $this->getOptions()),
        ]);
    }
}
