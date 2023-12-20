<?php

namespace Botble\SeoHelper\Contracts\Entities;

use Botble\SeoHelper\Contracts\RenderableContract;

interface OpenGraphContract extends RenderableContract
{
    /**
     * Set the open graph prefix.
     *
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix);

    /**
     * Set type property.
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Set title property.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Set description property.
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Set url property.
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * Set image property.
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image);

    /**
     * Set site name property.
     *
     * @param string $siteName
     * @return $this
     */
    public function setSiteName($siteName);

    /**
     * Add many open graph properties.
     *
     * @param array $properties
     * @return $this
     */
    public function addProperties(array $properties);

    /**
     * Add an open graph property.
     *
     * @param string $property
     * @param string $content
     * @return $this
     */
    public function addProperty($property, $content);

    /**
     * @return bool
     */
    public function hasImage();

    public function getProperty(string $property): string|null;
}
