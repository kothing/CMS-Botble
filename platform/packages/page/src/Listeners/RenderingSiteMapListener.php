<?php

namespace Botble\Page\Listeners;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Supports\RepositoryHelper;
use Botble\Page\Models\Page;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Botble\Theme\Facades\SiteMapManager;

class RenderingSiteMapListener
{
    public function handle(RenderingSiteMapEvent $event): void
    {
        if ($event->key == 'pages') {
            $pages = Page::query()
                ->where('status', BaseStatusEnum::PUBLISHED)
                ->orderBy('created_at', 'desc')
                ->select(['id', 'name', 'updated_at'])
                ->with('slugable');

            $pages = RepositoryHelper::applyBeforeExecuteQuery($pages, new Page())->get();

            foreach ($pages as $page) {
                SiteMapManager::add($page->url, $page->updated_at, '0.8');
            }
        }
    }
}
