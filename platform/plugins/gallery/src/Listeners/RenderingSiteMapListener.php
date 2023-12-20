<?php

namespace Botble\Gallery\Listeners;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Gallery\Facades\Gallery;
use Botble\Gallery\Repositories\Interfaces\GalleryInterface;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Botble\Theme\Facades\SiteMapManager;

class RenderingSiteMapListener
{
    public function __construct(protected GalleryInterface $galleryRepository)
    {
    }

    public function handle(RenderingSiteMapEvent $event): void
    {
        $lastUpdated = $this->galleryRepository
            ->getModel()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->latest('updated_at')
            ->value('updated_at');

        if ($event->key == 'galleries') {
            SiteMapManager::add(Gallery::getGalleriesPageUrl(), $lastUpdated, '0.8', 'weekly');

            $galleries = $this->galleryRepository->getDataSiteMap();

            foreach ($galleries as $gallery) {
                SiteMapManager::add($gallery->url, $gallery->updated_at, '0.8');
            }

            return;
        }

        SiteMapManager::addSitemap(SiteMapManager::route('galleries'), $lastUpdated);
    }
}
