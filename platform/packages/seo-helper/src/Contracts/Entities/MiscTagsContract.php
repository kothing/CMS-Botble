<?php

namespace Botble\SeoHelper\Contracts\Entities;

use Botble\SeoHelper\Contracts\RenderableContract;

interface MiscTagsContract extends RenderableContract
{
    /**
     * Get the current URL.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Set the current URL.
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * Make MiscTags instance.
     *
     * @param array $defaults
     * @return $this
     */
    public static function make(array $defaults = []);

    /**
     * Add a meta tag.
     *
     * @param string $name
     * @param string $content
     * @return $this
     */
    public function add($name, $content);

    /**
     * Add many meta tags.
     *
     * @param array $meta
     * @return $this
     */
    public function addMany(array $meta);

    /**
     * Remove a meta from the meta collection by key.
     *
     * @param array|string $names
     * @return $this
     */
    public function remove($names);

    /**
     * Reset the meta collection.
     *
     * @return $this
     */
    public function reset();
}
