<?php

namespace Botble\Sitemap;

use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactory;
use Illuminate\Filesystem\Filesystem as Filesystem;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\View\Factory as ViewFactory;

class Sitemap
{
    public ?Model $model = null;

    public ?CacheRepository $cache = null;

    protected ?ConfigRepository $configRepository = null;

    protected ?Filesystem $file = null;

    protected ?ResponseFactory $response = null;

    protected ?ViewFactory $view = null;

    public function __construct(
        array $config,
        CacheRepository $cache,
        ConfigRepository $configRepository,
        Filesystem $file,
        ResponseFactory $response,
        ViewFactory $view
    ) {
        $this->cache = $cache;
        $this->configRepository = $configRepository;
        $this->file = $file;
        $this->response = $response;
        $this->view = $view;

        $this->model = new Model($config);
    }

    public function setCache(string|null $key = null, $duration = null, bool $useCache = true): void
    {
        $this->model->setUseCache($useCache);

        if (null !== $key) {
            $this->model->setCacheKey($key);
        }

        if (null !== $duration) {
            $this->model->setCacheDuration($duration);
        }
    }

    /**
     * Add new sitemap item to $items array.
     */
    public function add(
        string $loc,
        string|null $lastMod = null,
        string|null $priority = null,
        string|null $freq = null,
        array $images = [],
        string|null $title = null,
        array $translations = [],
        array $videos = [],
        array $googleNews = [],
        array $alternates = []
    ): void {
        $params = [
            'loc' => $loc,
            'lastmod' => $lastMod,
            'priority' => $priority,
            'freq' => $freq,
            'images' => $images,
            'title' => $title,
            'translations' => $translations,
            'videos' => $videos,
            'googlenews' => $googleNews,
            'alternates' => $alternates,
        ];

        $this->addItem($params);
    }

    /**
     * Add new sitemap one or multiple items to $items array.
     */
    public function addItem(array $params = []): void
    {
        // if is multidimensional
        if (array_key_exists(1, $params)) {
            foreach ($params as $param) {
                $this->addItem($param);
            }

            return;
        }

        $loc = Arr::get($params, 'loc', '/');
        $lastMod = Arr::get($params, 'lastmod');
        $priority = Arr::get($params, 'priority');
        $freq = Arr::get($params, 'freq');
        $images = Arr::get($params, 'images', []);
        $title = Arr::get($params, 'title');
        $translations = Arr::get($params, 'translations', []);
        $videos = Arr::get($params, 'videos', []);
        $googleNews = Arr::get($params, 'googlenews', []);
        $alternates = Arr::get($params, 'alternates', []);

        // escaping
        if ($this->model->isEscaping()) {
            $loc = htmlentities($loc, ENT_XML1);

            if ($title != null) {
                $title = htmlentities($title, ENT_XML1);
            }

            if ($images) {
                foreach ($images as $k => $image) {
                    foreach ($image as $key => $value) {
                        $images[$k][$key] = htmlentities($value, ENT_XML1);
                    }
                }
            }

            if ($translations) {
                foreach ($translations as $k => $translation) {
                    foreach ($translation as $key => $value) {
                        $translations[$k][$key] = htmlentities($value, ENT_XML1);
                    }
                }
            }

            if ($alternates) {
                foreach ($alternates as $k => $alternate) {
                    foreach ($alternate as $key => $value) {
                        $alternates[$k][$key] = htmlentities($value, ENT_XML1);
                    }
                }
            }

            if ($videos) {
                foreach ($videos as $k => $video) {
                    if (! empty($video['title'])) {
                        $videos[$k]['title'] = htmlentities($video['title'], ENT_XML1);
                    }
                    if (! empty($video['description'])) {
                        $videos[$k]['description'] = htmlentities($video['description'], ENT_XML1);
                    }
                }
            }

            if ($googleNews) {
                if (isset($googleNews['sitename'])) {
                    $googleNews['sitename'] = htmlentities($googleNews['sitename'], ENT_XML1);
                }
            }
        }

        $googleNews['sitename'] = $googleNews['sitename'] ?? '';
        $googleNews['language'] = $googleNews['language'] ?? 'en';
        $googleNews['publication_date'] = $googleNews['publication_date'] ?? date('Y-m-d H:i:s');

        $this->model->setItems([
            'loc' => $loc,
            'lastmod' => $lastMod,
            'priority' => $priority,
            'freq' => $freq,
            'images' => $images,
            'title' => $title,
            'translations' => $translations,
            'videos' => $videos,
            'googlenews' => $googleNews,
            'alternates' => $alternates,
        ]);
    }

    /**
     * Add new sitemap to $sitemaps array.
     */
    public function resetSitemaps(array $sitemaps = []): void
    {
        $this->model->resetSitemaps($sitemaps);
    }

    /**
     * Returns document with all sitemap items from $items array.
     *
     * @param string $format (options: xml, html, txt, ror-rss, ror-rdf, google-news)
     * @return Response
     */
    public function render(string $format = 'xml')
    {
        // limit size of sitemap
        if ($this->model->getMaxSize() > 0 && count($this->model->getItems()) > $this->model->getMaxSize()) {
            $this->model->limitSize($this->model->getMaxSize());
        } elseif ('google-news' == $format && count($this->model->getItems()) > 1000) {
            $this->model->limitSize(1000);
        } elseif ('google-news' != $format && count($this->model->getItems()) > 50000) {
            $this->model->limitSize();
        }

        $data = $this->generate($format);

        return $this->response->make($data['content'], 200, $data['headers']);
    }

    /**
     * Generates document with all sitemap items from $items array.
     *
     * @param string $format (options: xml, html, txt, ror-rss, ror-rdf, sitemapindex, google-news)
     */
    public function generate(string $format = 'xml'): array
    {
        // check if caching is enabled, there is a cached content and its duration isn't expired
        if ($this->isCached()) {
            ('sitemapindex' == $format) ? $this->model->resetSitemaps(
                $this->cache->get($this->model->getCacheKey())
            ) : $this->model->resetItems($this->cache->get($this->model->getCacheKey()));
        } elseif ($this->model->isUseCache()) {
            ('sitemapindex' == $format) ? $this->cache->put(
                $this->model->getCacheKey(),
                $this->model->getSitemaps(),
                $this->model->getCacheDuration()
            ) : $this->cache->put(
                $this->model->getCacheKey(),
                $this->model->getItems(),
                $this->model->getCacheDuration()
            );
        }

        if (! $this->model->getLink()) {
            $this->model->setLink($this->configRepository->get('app.url'));
        }

        if (! $this->model->getTitle()) {
            $this->model->setTitle('Sitemap for ' . $this->model->getLink());
        }

        $channel = [
            'title' => $this->model->getTitle(),
            'link' => $this->model->getLink(),
        ];

        // check if styles are enabled
        if ($this->model->isUseStyles()) {
            if (null != $this->model->getSloc() && file_exists(
                public_path($this->model->getSloc() . $format . '.xsl')
            )) {
                // use style from your custom location
                $style = $this->model->getSloc() . $format . '.xsl';
            } else {
                // don't use style
                $style = null;
            }
        } else {
            // don't use style
            $style = null;
        }

        return match ($format) {
            'ror-rss' => [
                'content' => $this->view->make(
                    'packages/sitemap::ror-rss',
                    ['items' => $this->model->getItems(), 'channel' => $channel, 'style' => $style]
                )->render(),
                'headers' => ['Content-type' => 'text/rss+xml; charset=utf-8'],
            ],
            'ror-rdf' => [
                'content' => $this->view->make(
                    'packages/sitemap::ror-rdf',
                    ['items' => $this->model->getItems(), 'channel' => $channel, 'style' => $style]
                )->render(),
                'headers' => ['Content-type' => 'text/rdf+xml; charset=utf-8'],
            ],
            'html' => [
                'content' => $this->view->make(
                    'packages/sitemap::html',
                    ['items' => $this->model->getItems(), 'channel' => $channel, 'style' => $style]
                )->render(),
                'headers' => ['Content-type' => 'text/html; charset=utf-8'],
            ],
            'txt' => [
                'content' => $this->view->make(
                    'packages/sitemap::txt',
                    ['items' => $this->model->getItems(), 'style' => $style]
                )->render(),
                'headers' => ['Content-type' => 'text/plain; charset=utf-8'],
            ],
            'sitemapindex' => [
                'content' => $this->view->make(
                    'packages/sitemap::sitemapindex',
                    ['sitemaps' => $this->model->getSitemaps(), 'style' => $style]
                )->render(),
                'headers' => ['Content-type' => 'text/xml; charset=utf-8'],
            ],
            default => [
                'content' => $this->view->make(
                    'packages/sitemap::' . $format,
                    ['items' => $this->model->getItems(), 'style' => $style]
                )->render(),
                'headers' => ['Content-type' => 'text/xml; charset=utf-8'],
            ],
        };
    }

    public function isCached(): bool
    {
        return $this->model->isUseCache() && $this->cache->has($this->model->getCacheKey());
    }

    /**
     * Generate sitemap and store it to a file.
     *
     * @param string $format (options: xml, html, txt, ror-rss, ror-rdf, sitemapindex, google-news)
     * @param string $filename (without file extension, may be a path like 'sitemaps/sitemap1' but must exist)
     * @param string|null $path (path to store sitemap like '/www/site/public')
     * @param string|null $style (path to custom xls style like '/styles/xsl/xml-sitemap.xsl')
     * @return void
     */
    public function store(
        string $format = 'xml',
        string $filename = 'sitemap',
        string|null $path = null,
        string|null $style = null
    ) {
        // turn off caching for this method
        $this->model->setUseCache(false);

        // use correct file extension
        in_array($format, ['txt', 'html'], true) ? $fe = $format : $fe = 'xml';

        if ($this->model->getUseGzip()) {
            $fe = $fe . '.gz';
        }

        // use custom size limit for sitemaps
        if ($this->model->getMaxSize() > 0 && count($this->model->getItems()) > $this->model->getMaxSize()) {
            if ($this->model->isUseLimitSize()) {
                // limit size
                $this->model->limitSize($this->model->getMaxSize());
                $data = $this->generate($format);
            } else {
                // use sitemapindex and generate partial sitemaps
                foreach (array_chunk($this->model->getItems(), $this->model->getMaxSize()) as $key => $item) {
                    // reset current items
                    $this->model->resetItems($item);

                    // generate new partial sitemap
                    $this->store($format, $filename . '-' . $key, $path);

                    // add sitemap to sitemapindex
                    if ($path != null) {
                        // if using custom path generate relative urls for sitemaps in the sitemapindex
                        $this->addSitemap($filename . '-' . $key . '.' . $fe);
                    } else {
                        // else generate full urls based on app's domain
                        $this->addSitemap(url($filename . '-' . $key . '.' . $fe));
                    }
                }

                $data = $this->generate('sitemapindex');
            }
        } elseif (('google-news' != $format && count(
            $this->model->getItems()
        ) > 50000) || ($format == 'google-news' && count($this->model->getItems()) > 1000)) {
            ('google-news' != $format) ? $max = 50000 : $max = 1000;

            // check if limiting size of items array is enabled
            if (! $this->model->isUseLimitSize()) {
                // use sitemapindex and generate partial sitemaps
                foreach (array_chunk($this->model->getItems(), $max) as $key => $item) {
                    // reset current items
                    $this->model->resetItems($item);

                    // generate new partial sitemap
                    $this->store($format, $filename . '-' . $key, $path, $style);

                    // add sitemap to sitemapindex
                    if (null != $path) {
                        // if using custom path generate relative urls for sitemaps in the sitemapindex
                        $this->addSitemap($filename . '-' . $key . '.' . $fe);
                    } else {
                        // else generate full urls based on app's domain
                        $this->addSitemap(url($filename . '-' . $key . '.' . $fe));
                    }
                }

                $data = $this->generate('sitemapindex');
            } else {
                // reset items and use only most recent $max items
                $this->model->limitSize($max);
                $data = $this->generate($format);
            }
        } else {
            $data = $this->generate($format);
        }

        // clear memory
        if ('sitemapindex' == $format) {
            $this->model->resetSitemaps();
        }

        $this->model->resetItems();

        // if custom path
        if (null == $path) {
            $file = public_path() . DIRECTORY_SEPARATOR . $filename . '.' . $fe;
        } else {
            $file = $path . DIRECTORY_SEPARATOR . $filename . '.' . $fe;
        }

        if ($this->model->getUseGzip()) {
            // write file (gzip compressed)
            $this->file->put($file, gzencode($data['content'], 9));
        } else {
            // write file
            $this->file->put($file, $data['content']);
        }
    }

    /**
     * Add new sitemap to $sitemaps array.
     */
    public function addSitemap(string $loc, string|null $lastMod = null): void
    {
        $this->model->setSitemaps([
            'loc' => $loc,
            'lastmod' => $lastMod,
        ]);
    }
}
