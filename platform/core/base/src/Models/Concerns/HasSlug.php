<?php

namespace Botble\Base\Models\Concerns;

use Botble\Slug\Facades\SlugHelper;
use Illuminate\Support\Str;

trait HasSlug
{
    public static function createSlug(string|null $name, int|string|null $id, string $fromColumn = 'slug'): string
    {
        $language = ! SlugHelper::turnOffAutomaticUrlTranslationIntoLatin() ? 'en' : false;

        $slug = Str::slug($name, '-', $language);
        $index = 1;
        $baseSlug = $slug;

        while (self::query()->where($fromColumn, $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $index++;
        }

        if (empty($slug)) {
            $slug = time();
        }

        return $slug;
    }
}
