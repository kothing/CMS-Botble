<?php

use Botble\Widget\AbstractWidget;
use Illuminate\Support\Collection;

class TagsWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Tags'),
            'description' => __('Popular tags'),
            'number_display' => 5,
        ]);
    }

    protected function data(): array|Collection
    {
        return [
            'tags' => get_popular_tags($this->getConfig()['number_display']),
        ];
    }
}
