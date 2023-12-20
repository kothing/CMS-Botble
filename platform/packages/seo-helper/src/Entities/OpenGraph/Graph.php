<?php

namespace Botble\SeoHelper\Entities\OpenGraph;

use Botble\SeoHelper\Contracts\Entities\MetaCollectionContract;
use Botble\SeoHelper\Contracts\Entities\OpenGraphContract;
use Botble\SeoHelper\Contracts\Helpers\MetaContract;
use Illuminate\Support\Collection;

class Graph implements OpenGraphContract
{
    protected MetaCollectionContract|Collection|MetaContract $meta;

    public function __construct()
    {
        $this->meta = new MetaCollection();
        $this->setSiteName(theme_option('site_title'));
    }

    /**
     * Set the open graph prefix.
     *
     * @param string $prefix
     *
     * @return Graph
     */
    public function setPrefix($prefix)
    {
        $this->meta->setPrefix($prefix);

        return $this;
    }

    /**
     * Set type property.
     *
     * @param string $type
     *
     * @return Graph
     */
    public function setType($type)
    {
        return $this->addProperty('type', $type);
    }

    /**
     * Set title property.
     *
     * @param string $title
     *
     * @return Graph
     */
    public function setTitle($title)
    {
        return $this->addProperty('title', $title);
    }

    /**
     * Set description property.
     *
     * @param string $description
     *
     * @return Graph
     */
    public function setDescription($description)
    {
        return $this->addProperty('description', $description);
    }

    /**
     * Set url property.
     *
     * @param string $url
     *
     * @return Graph
     */
    public function setUrl($url)
    {
        return $this->addProperty('url', $url);
    }

    /**
     * Set image property.
     *
     * @param string $image
     *
     * @return Graph
     */
    public function setImage($image)
    {
        return $this->addProperty('image', $image);
    }

    /**
     * @return bool
     */
    public function hasImage()
    {
        return $this->meta->has('image');
    }

    /**
     * Set site name property.
     *
     * @param string $siteName
     *
     * @return Graph
     */
    public function setSiteName($siteName)
    {
        return $this->addProperty('site_name', $siteName);
    }

    /**
     * Add many open graph properties.
     *
     * @param array $properties
     *
     * @return Graph
     */
    public function addProperties(array $properties)
    {
        $this->meta->addMany($properties);

        return $this;
    }

    /**
     * Add an open graph property.
     *
     * @param string $name
     * @param string $content
     *
     * @return Graph
     */
    public function addProperty($name, $content)
    {
        $this->meta->add(compact('name', 'content'));

        return $this;
    }

    public function getProperty(string $name): string|null
    {
        if (! $this->meta->has($name)) {
            return null;
        }

        $meta = $this->meta->get($name);

        return (string)$meta?->getContent();
    }

    /**
     * Render the tag.
     *
     * @return string
     */
    public function render()
    {
        return $this->meta->render();
    }

    /**
     * Render the tag.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
