<?php

namespace Botble\SeoHelper\Contracts\Entities;

use Botble\SeoHelper\Contracts\RenderableContract;

interface TitleContract extends RenderableContract
{
    public function getTitleOnly(): string|null;

    public function set(string|null $title);

    /**
     * Get site name.
     *
     * @return string
     */
    public function getSiteName();

    /**
     * Set site name.
     *
     * @param string $siteName
     * @return $this
     */
    public function setSiteName($siteName);

    /**
     * Get title separator.
     *
     * @return string
     */
    public function getSeparator();

    /**
     * Set title separator.
     *
     * @param string $separator
     * @return $this
     */
    public function setSeparator($separator);

    /**
     * Set title first.
     *
     * @return $this
     */
    public function setFirst();

    /**
     * Set title last.
     *
     * @return $this
     */
    public function setLast();

    /**
     * Check if title is first.
     *
     * @return bool
     */
    public function isTitleFirst();

    /**
     * Get title max length.
     *
     * @return int
     */
    public function getMax();

    /**
     * Set title max length.
     *
     * @param int $max
     * @return $this
     */
    public function setMax($max);

    /**
     * Make a Title instance.
     *
     * @param string $title
     * @param string $siteName
     * @param string $separator
     * @return $this
     */
    public static function make($title, $siteName = '', $separator = '-');

    /**
     * Get the title.
     *
     * @return string
     */
    public function getTitle();
}
