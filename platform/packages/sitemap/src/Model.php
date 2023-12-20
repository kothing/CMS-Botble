<?php

namespace Botble\Sitemap;

use Carbon\Carbon;

class Model
{
    protected array $items = [];

    protected array $sitemaps = [];

    protected string|null $title = null;

    protected string|null $link = null;

    protected mixed $useStyles = true;

    protected string $sloc = '/vendor/core/packages/sitemap/styles/';

    protected bool $useCache = false;

    protected string $cacheKey = 'cms-sitemap.';

    protected int $cacheDuration = 60;

    protected bool $escaping = true;

    protected bool $useLimitSize = false;

    protected bool|int|null $maxSize = null;

    protected bool $useGzip = false;

    /**
     * Populating model variables from configuration file.
     */
    public function __construct(array $config)
    {
        $this->useCache = $config['use_cache'] ?? $this->useCache;
        $this->cacheKey = $config['cache_key'] ?? $this->cacheKey;
        $this->cacheDuration = $config['cache_duration'] ?? $this->cacheDuration;
        $this->escaping = $config['escaping'] ?? $this->escaping;
        $this->useLimitSize = $config['use_limit_size'] ?? $this->useLimitSize;
        $this->useStyles = $config['use_styles'] ?? $this->useStyles;
        $this->sloc = $config['styles_location'] ?? $this->sloc;
        $this->maxSize = $config['max_size'] ?? $this->maxSize;
        $this->useGzip = $config['use_gzip'] ?? $this->useGzip;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getSitemaps(): array
    {
        return $this->sitemaps;
    }

    public function getTitle(): string|null
    {
        return $this->title;
    }

    public function getLink(): string|null
    {
        return $this->link;
    }

    public function isUseStyles(): bool
    {
        return $this->useStyles;
    }

    public function getSloc(): string
    {
        return $this->sloc;
    }

    public function isUseCache(): bool
    {
        return $this->useCache;
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey . route('public.index');
    }

    public function getCacheDuration(): int|string
    {
        return $this->cacheDuration;
    }

    public function isEscaping(): bool
    {
        return $this->escaping;
    }

    public function isUseLimitSize(): bool
    {
        return $this->useLimitSize;
    }

    /**
     * Returns $maxSize value.
     *
     * @return bool|mixed|null
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    /**
     * Returns $useGzip value.
     *
     * @return bool|mixed
     */
    public function getUseGzip()
    {
        return $this->useGzip;
    }

    /**
     * Sets $escaping value.
     *
     * @param bool $escaping
     */
    public function setEscaping($escaping)
    {
        $this->escaping = $escaping;
    }

    /**
     * Adds item to $items array.
     *
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items[] = $items;
    }

    /**
     * Adds sitemap to $sitemaps array.
     *
     * @param array $sitemap
     */
    public function setSitemaps($sitemap)
    {
        $this->sitemaps[] = $sitemap;
    }

    /**
     * Sets $title value.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Sets $link value.
     *
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Sets $useStyles value.
     *
     * @param bool $useStyles
     */
    public function setUseStyles($useStyles)
    {
        $this->useStyles = $useStyles;
    }

    /**
     * Sets $sloc value.
     *
     * @param string $sloc
     */
    public function setSloc($sloc)
    {
        $this->sloc = $sloc;
    }

    /**
     * Sets $useLimitSize value.
     *
     * @param bool $useLimitSize
     */
    public function setUseLimitSize($useLimitSize)
    {
        $this->useLimitSize = $useLimitSize;
    }

    /**
     * Sets $maxSize value.
     *
     * @param int $maxSize
     */
    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;
    }

    /**
     * Sets $useGzip value.
     *
     * @param bool $useGzip
     */
    public function setUseGzip($useGzip = true)
    {
        $this->useGzip = $useGzip;
    }

    /**
     * Limit size of $items array to 50000 elements (1000 for google-news).
     */
    public function limitSize($max = 50000)
    {
        $this->items = array_slice($this->items, 0, $max);
    }

    /**
     * Reset $items array.
     *
     * @param array $items
     */
    public function resetItems($items = [])
    {
        $this->items = $items;
    }

    /**
     * Reset $sitemaps array.
     *
     * @param array $sitemaps
     */
    public function resetSitemaps($sitemaps = [])
    {
        $this->sitemaps = $sitemaps;
    }

    /**
     * Set use cache value.
     *
     * @param bool $useCache
     */
    public function setUseCache($useCache = true)
    {
        $this->useCache = $useCache;
    }

    /**
     * Set cache key value.
     *
     * @param string $cacheKey
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    /**
     * Set cache duration value.
     *
     * @param Carbon|\Datetime|int $cacheDuration
     */
    public function setCacheDuration($cacheDuration)
    {
        $this->cacheDuration = $cacheDuration;
    }
}
