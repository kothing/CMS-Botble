<?php

use Botble\Widget\AbstractWidget;

class TagsWidget extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $widgetDirectory = 'tags';

    /**
     * TagsWidget constructor.
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __construct()
    {
        parent::__construct([
            'name' => __('Tags'),
            'description' => __('Popular tags'),
            'number_display' => 5,
        ]);
    }
}
