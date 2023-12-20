<?php

namespace Botble\SeoHelper\Contracts\Helpers;

use Botble\SeoHelper\Contracts\RenderableContract;

interface MetaContract extends RenderableContract
{
    /**
     * Get the meta name.
     *
     * @return string
     */
    public function key();

    /**
     * Set the meta prefix name.
     *
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix);

    /**
     * Set the meta property name.
     *
     * @param string $nameProperty
     * @return $this
     */
    public function setNameProperty($nameProperty);

    /**
     * Make Meta instance.
     *
     * @param string $name
     * @param string $content
     * @param string $propertyName
     * @param string $prefix
     * @return $this
     */
    public static function make($name, $content, $propertyName = 'name', $prefix = '');

    /**
     * Check if meta is valid.
     *
     * @return bool
     */
    public function isValid();
}
