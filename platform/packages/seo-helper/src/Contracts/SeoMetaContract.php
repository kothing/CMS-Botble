<?php

namespace Botble\SeoHelper\Contracts;

use Botble\SeoHelper\Contracts\Entities\AnalyticsContract;
use Botble\SeoHelper\Contracts\Entities\DescriptionContract;
use Botble\SeoHelper\Contracts\Entities\MiscTagsContract;
use Botble\SeoHelper\Contracts\Entities\TitleContract;
use Botble\SeoHelper\Contracts\Entities\WebmastersContract;

interface SeoMetaContract extends RenderableContract
{
    /**
     * Set the Title instance.
     *
     * @param TitleContract $title
     * @return $this
     */
    public function title(TitleContract $title);

    /**
     * Set the Description instance.
     *
     * @param DescriptionContract $description
     * @return $this
     */
    public function description(DescriptionContract $description);

    /**
     * Set the MiscTags instance.
     *
     * @param MiscTagsContract $misc
     * @return $this
     */
    public function misc(MiscTagsContract $misc);

    /**
     * Set the Webmasters instance.
     *
     * @param WebmastersContract $webmasters
     * @return $this
     */
    public function webmasters(WebmastersContract $webmasters);

    /**
     * Set the title.
     *
     * @param string $title
     * @param string $siteName
     * @param string $separator
     * @return $this
     */
    public function setTitle($title, $siteName = null, $separator = null);

    /**
     * Set the description content.
     *
     * @param string $content
     * @return $this
     */
    public function setDescription($content);

    /**
     * Add a webmaster tool site verifier.
     *
     * @param string $webmaster
     * @param string $content
     * @return $this
     */
    public function addWebmaster($webmaster, $content);

    /**
     * Set the current URL.
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * Set the Google Analytics code.
     *
     * @param string $code
     * @return $this
     */
    public function setGoogleAnalytics($code);

    /**
     * Add a meta tag.
     *
     * @param string $name
     * @param string $content
     * @return $this
     */
    public function addMeta($name, $content);

    /**
     * Add many meta tags.
     *
     * @param array $meta
     * @return $this
     */
    public function addMetas(array $meta);

    public function getAnalytics(): AnalyticsContract;

    public function getTitle();

    public function getDescription();

    public function setGoogle($code);
}
