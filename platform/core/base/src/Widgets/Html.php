<?php

namespace Botble\Base\Widgets;

abstract class Html extends Widget
{
    protected string $view = 'html';

    public function getContent(): string
    {
        return '';
    }

    public function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'content' => $this->getContent(),
        ]);
    }
}
