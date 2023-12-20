<?php

namespace Botble\Slug\Services;

use Botble\Slug\Facades\SlugHelper;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Illuminate\Support\Str;

class SlugService
{
    public function __construct(protected SlugInterface $slugRepository)
    {
    }

    public function create(string|null $name, int|null $slugId = 0, $model = null): string|null
    {
        $slug = Str::slug($name, '-', ! SlugHelper::turnOffAutomaticUrlTranslationIntoLatin() ? 'en' : false);

        $index = 1;
        $baseSlug = $slug;

        $prefix = null;
        if (! empty($model)) {
            $prefix = SlugHelper::getPrefix($model);
        }

        while ($this->checkIfExistedSlug($slug, $slugId, $prefix)) {
            $slug = apply_filters(FILTER_SLUG_EXISTED_STRING, $baseSlug . '-' . $index++, $baseSlug, $index, $model);
        }

        if (empty($slug)) {
            $slug = time();
        }

        return apply_filters(FILTER_SLUG_STRING, $slug, $model);
    }

    protected function checkIfExistedSlug(string|null $slug, int|string|null $slugId, string|null $prefix): bool
    {
        return $this->slugRepository
            ->getModel()
            ->where([
                'key' => $slug,
                'prefix' => $prefix,
            ])
            ->where('id', '!=', (int)$slugId)
            ->exists();
    }
}
