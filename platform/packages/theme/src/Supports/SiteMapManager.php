<?php

namespace Botble\Theme\Supports;

use Botble\Base\Facades\BaseHelper;
use Botble\Sitemap\Sitemap;
use Botble\Slug\Facades\SlugHelper;
use Carbon\Carbon;
use Illuminate\Http\Response;

class SiteMapManager
{
    protected array $keys = ['sitemap', 'pages'];

    protected string $extension = 'xml';

    protected string $defaultDate = '2023-06-01 00:00';

    public function __construct(protected Sitemap $siteMap)
    {
    }

    public function init(string|null $prefix = null, string $extension = 'xml'): self
    {
        // create new site map object
        $this->siteMap = app('sitemap');
        // set cache (key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean))
        // by default cache is disabled
        $this->siteMap->setCache('cache_site_map_key' . $prefix . $extension, setting('cache_time_site_map', 60), setting('enable_cache_site_map', true));

        if ($prefix == 'pages' && ! BaseHelper::getHomepageId()) {
            $this->add(route('public.index'), Carbon::now()->toDateTimeString());
        }

        $this->extension = $extension;

        if (! $prefix) {
            $this->addSitemap($this->route('pages'));
        }

        return $this;
    }

    public function addSitemap(string $loc, string|null $lastModified = null): self
    {
        if (! $this->isCached()) {
            $this->siteMap->addSitemap($loc, $lastModified ?: $this->defaultDate);
        }

        return $this;
    }

    public function route(string|null $key = null): string
    {
        return route('public.sitemap.index', [$key, $this->extension]);
    }

    public function add(string $url, string|null $date = null, string $priority = '1.0', string $sequence = 'daily'): self
    {
        if (! $this->isCached()) {
            $this->siteMap->add($url, $date ?: $this->defaultDate, $priority, $sequence);
        }

        return $this;
    }

    public function isCached(): bool
    {
        return $this->siteMap->isCached();
    }

    public function getSiteMap(): Sitemap
    {
        return $this->siteMap;
    }

    public function render(string $type = 'xml'): Response
    {
        // show your site map (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
        return $this->siteMap->render($type);
    }

    public function getKeys(): array
    {
        return $this->keys;
    }

    public function registerKey(string|array $key, string|null $value = null): self
    {
        if (is_array($key)) {
            $this->keys = array_merge($this->keys, $key);
        } else {
            $this->keys[$key] = $value ?: $key;
        }

        return $this;
    }

    public function allowedExtensions(): array
    {
        $extensions = ['xml', 'html', 'txt', 'ror-rss', 'ror-rdf'];

        $slugPostfix = SlugHelper::getPublicSingleEndingURL();

        if (! $slugPostfix) {
            return $extensions;
        }

        $slugPostfix = trim($slugPostfix, '.');

        return array_filter($extensions, fn ($item) => $item != $slugPostfix);
    }
}
