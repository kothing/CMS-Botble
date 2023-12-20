<?php

namespace Botble\Gallery\Http\Controllers;

use Botble\Gallery\Facades\Gallery;
use Botble\Gallery\Models\Gallery as GalleryModel;
use Botble\Gallery\Repositories\Interfaces\GalleryInterface;
use Botble\Gallery\Services\GalleryService;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Events\RenderingSingleEvent;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Controller;

class PublicController extends Controller
{
    public function __construct(protected GalleryInterface $galleryRepository)
    {
    }

    public function getGalleries()
    {
        Gallery::registerAssets();

        $galleries = $this->galleryRepository->getAll();

        SeoHelper::setTitle(__('Galleries'));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Galleries'), Gallery::getGalleriesPageUrl());

        return Theme::scope('galleries', compact('galleries'), 'plugins/gallery::themes.galleries')
            ->render();
    }

    public function getGallery(string $slug, GalleryService $galleryService)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(GalleryModel::class));

        if (! $slug) {
            abort(404);
        }

        $data = $galleryService->handleFrontRoutes($slug);

        if (isset($data['slug']) && $data['slug'] !== $slug->key) {
            return redirect()
                ->to(route('public.single', SlugHelper::getPrefix(GalleryModel::class) . '/' . $data['slug']));
        }

        event(new RenderingSingleEvent($slug));

        return Theme::scope($data['view'], $data['data'], $data['default_view'])
            ->render();
    }
}
