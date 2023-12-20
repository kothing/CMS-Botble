<?php

use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Widget\AbstractWidget;
use Illuminate\Support\Collection;

class RecentPostsWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Recent posts'),
            'description' => __('Recent posts widget.'),
            'number_display' => 5,
        ]);
    }

    protected function data(): array|Collection
    {
        if (! is_plugin_active('blog')) {
            return ['posts' => []];
        }

        return [
            'posts' => app(PostInterface::class)->getRecentPosts($this->getConfig()['number_display']),
        ];
    }
}
