<?php

namespace Botble\Base\Widgets;

use Botble\Base\Helpers\ChartHelper;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

abstract class Widget
{
    protected string $view;

    protected int $columns;

    protected CarbonInterface $endDate;

    protected CarbonInterface $startDate;

    protected string $dateFormat;

    public function __construct()
    {
        [$this->startDate, $this->endDate] = ChartHelper::getDateRange();

        $diffInDays = $this->startDate->diffInDays($this->endDate);

        $this->dateFormat = match (true) {
            $diffInDays < 1 => '%h %d',
            $diffInDays <= 30 => '%d %b',
            $diffInDays > 30 => '%b %Y',
            $diffInDays > 365 => '%Y',
            default => '%d %b %Y',
        };
    }

    public function getLabel(): string|null
    {
        return null;
    }

    public function getPriority(): int|null
    {
        return null;
    }

    public function getColumns(): int
    {
        return 12;
    }

    public function getViewData(): array
    {
        return [
            'id' => strtolower(Str::snake(class_basename(static::class . 'Widget'), '-')),
            'label' => $this->getLabel(),
            'priority' => $this->getPriority(),
            'columns' => $this->columns ?? null,
        ];
    }

    public function render(): View
    {
        return view('core/base::widgets.' . $this->view, $this->getViewData());
    }
}
