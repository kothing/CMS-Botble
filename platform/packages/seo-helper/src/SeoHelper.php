<?php

namespace Botble\SeoHelper;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Models\BaseModel;
use Botble\SeoHelper\Contracts\SeoHelperContract;
use Botble\SeoHelper\Contracts\SeoMetaContract;
use Botble\SeoHelper\Contracts\SeoOpenGraphContract;
use Botble\SeoHelper\Contracts\SeoTwitterContract;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SeoHelper implements SeoHelperContract
{
    public function __construct(
        protected SeoMetaContract $seoMeta,
        protected SeoOpenGraphContract $seoOpenGraph,
        protected SeoTwitterContract $seoTwitter
    ) {
        $this->openGraph()->addProperty('type', 'website');
    }

    public function setSeoMeta(SeoMetaContract $seoMeta): self
    {
        $this->seoMeta = $seoMeta;

        return $this;
    }

    public function setSeoOpenGraph(SeoOpenGraphContract $seoOpenGraph): self
    {
        $this->seoOpenGraph = $seoOpenGraph;

        return $this;
    }

    public function setSeoTwitter(SeoTwitterContract $seoTwitter): self
    {
        $this->seoTwitter = $seoTwitter;

        return $this;
    }

    public function openGraph(): SeoOpenGraphContract
    {
        return $this->seoOpenGraph;
    }

    public function setTitle(string|null $title, string|null $siteName = null, string|null $separator = null): self
    {
        $this->meta()->setTitle($title, $siteName, $separator);
        $this->openGraph()->setTitle($title);
        if ($siteName) {
            $this->openGraph()->setSiteName($siteName);
        }
        $this->twitter()->setTitle($title);

        return $this;
    }

    public function meta(): SeoMetaContract
    {
        return $this->seoMeta;
    }

    public function twitter(): SeoTwitterContract
    {
        return $this->seoTwitter;
    }

    public function getTitle(): string|null
    {
        return $this->meta()->getTitle();
    }

    public function getDescription(): string|null
    {
        return $this->meta()->getDescription();
    }

    public function setDescription($description): self
    {
        $description = BaseHelper::cleanShortcodes($description);

        $this->meta()->setDescription($description);
        $this->openGraph()->setDescription($description);
        $this->twitter()->setDescription($description);

        return $this;
    }

    public function __toString()
    {
        return $this->render();
    }

    public function render()
    {
        return implode(
            PHP_EOL,
            array_filter([
                $this->meta()->render(),
                $this->openGraph()->render(),
                $this->twitter()->render(),
            ])
        );
    }

    public function saveMetaData(string $screen, Request $request, BaseModel $object): bool
    {
        if (in_array(get_class($object), config('packages.seo-helper.general.supported', [])) && $request->has(
            'seo_meta'
        )) {
            try {
                if (empty($request->input('seo_meta'))) {
                    MetaBox::deleteMetaData($object, 'seo_meta');

                    return false;
                }

                $seoMeta = $request->input('seo_meta', []);

                if (! Arr::get($seoMeta, 'seo_title')) {
                    Arr::forget($seoMeta, 'seo_title');
                }

                if (! Arr::get($seoMeta, 'seo_description')) {
                    Arr::forget($seoMeta, 'seo_description');
                }

                if (! empty($seoMeta)) {
                    MetaBox::saveMetaBoxData($object, 'seo_meta', $seoMeta);
                } else {
                    MetaBox::deleteMetaData($object, 'seo_meta');
                }

                return true;
            } catch (Exception) {
                return false;
            }
        }

        return false;
    }

    public function deleteMetaData(string $screen, BaseModel $object): bool
    {
        try {
            if (in_array(get_class($object), config('packages.seo-helper.general.supported', []))) {
                MetaBox::deleteMetaData($object, 'seo_meta');
            }

            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function registerModule(array|string $model): self
    {
        if (! is_array($model)) {
            $model = [$model];
        }

        config([
            'packages.seo-helper.general.supported' => array_merge(
                config('packages.seo-helper.general.supported', []),
                $model
            ),
        ]);

        return $this;
    }
}
