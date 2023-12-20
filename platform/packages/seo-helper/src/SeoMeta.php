<?php

namespace Botble\SeoHelper;

use Botble\SeoHelper\Contracts\Entities\AnalyticsContract;
use Botble\SeoHelper\Contracts\Entities\DescriptionContract;
use Botble\SeoHelper\Contracts\Entities\MiscTagsContract;
use Botble\SeoHelper\Contracts\Entities\TitleContract;
use Botble\SeoHelper\Contracts\Entities\WebmastersContract;
use Botble\SeoHelper\Contracts\SeoMetaContract;

class SeoMeta implements SeoMetaContract
{
    /**
     * The Title instance.
     *
     * @var TitleContract
     */
    protected $title;

    /**
     * The Description instance.
     *
     * @var DescriptionContract
     */
    protected $description;

    /**
     * The MiscTags instance.
     *
     * @var MiscTagsContract
     */
    protected $misc;

    /**
     * The Webmasters instance.
     *
     * @var WebmastersContract
     */
    protected $webmasters;

    /**
     * The Analytics instance.
     *
     * @var AnalyticsContract
     */
    protected $analytics;

    protected string|null $currentUrl = null;

    /**
     * Make SeoMeta instance.
     */
    public function __construct()
    {
        $this->title(new Entities\Title());
        $this->description(new Entities\Description());
        $this->misc(new Entities\MiscTags());
        $this->webmasters(new Entities\Webmasters());
        $this->analytics(new Entities\Analytics());
    }

    /**
     * Set the Title instance.
     *
     * @param TitleContract $title
     *
     * @return $this
     */
    public function title(TitleContract $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the Description instance.
     *
     * @param DescriptionContract $description
     *
     * @return $this
     */
    public function description(DescriptionContract $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the MiscTags instance.
     *
     * @param MiscTagsContract $misc
     *
     * @return $this
     */
    public function misc(MiscTagsContract $misc)
    {
        $this->misc = $misc;

        return $this;
    }

    /**
     * Set the Webmasters instance.
     *
     * @param WebmastersContract $webmasters
     *
     * @return $this
     */
    public function webmasters(WebmastersContract $webmasters)
    {
        $this->webmasters = $webmasters;

        return $this;
    }

    /**
     * Set the Analytics instance.
     *
     * @param AnalyticsContract $analytics
     *
     * @return $this
     */
    protected function analytics(AnalyticsContract $analytics)
    {
        $this->analytics = $analytics;

        return $this;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setGoogle($code)
    {
        $this->analytics->setGoogle($code);

        return $this;
    }

    /**
     * Get the title.
     *
     * @return string
     */
    public function getTitle()
    {
        $title = $this->title->getTitleOnly();

        if (! theme_option('show_site_name') && $title) {
            return $title;
        }

        return $this->title->getTitle();
    }

    /**
     * Set the title.
     *
     * @param string $title
     * @param string $siteName
     * @param string $separator
     *
     * @return $this
     */
    public function setTitle($title, $siteName = null, $separator = null)
    {
        if (! empty($title)) {
            $this->title->set($title);
        }

        if (! empty($siteName)) {
            $this->title->setSiteName($siteName);
        }

        if (! empty($separator)) {
            $this->title->setSeparator($separator);
        }

        return $this;
    }

    /**
     * Set the description content.
     *
     * @param string $content
     *
     * @return $this
     */
    public function setDescription($content)
    {
        $this->description->set($content);

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description->getContent();
    }

    /**
     * Add a webmaster tool site verifier.
     *
     * @param string $webmaster
     * @param string $content
     *
     * @return $this
     */
    public function addWebmaster($webmaster, $content)
    {
        $this->webmasters->add($webmaster, $content);

        return $this;
    }

    /**
     * Set the current URL.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->currentUrl = $url;
        $this->misc->setUrl($url);

        return $this;
    }

    /**
     * Set the Google Analytics code.
     *
     * @param string $code
     *
     * @return $this
     */
    public function setGoogleAnalytics($code)
    {
        $this->analytics->setGoogle($code);

        return $this;
    }

    /**
     * Add a meta tag.
     *
     * @param string $name
     * @param string $content
     *
     * @return $this
     */
    public function addMeta($name, $content)
    {
        $this->misc->add($name, $content);

        return $this;
    }

    /**
     * Add many meta tags.
     *
     * @param array $meta
     *
     * @return $this
     */
    public function addMetas(array $meta)
    {
        $this->misc->addMany($meta);

        return $this;
    }

    public function getAnalytics(): AnalyticsContract
    {
        return $this->analytics;
    }

    /**
     * Render all seo tags.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Render all seo tags.
     *
     * @return string
     */
    public function render()
    {
        return implode(PHP_EOL, array_filter([
            $this->title->render(),
            $this->description->render(),
            $this->misc->render(),
            $this->webmasters->render(),
        ]));
    }
}
