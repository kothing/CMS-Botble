<?php

namespace Botble\SeoHelper\Bases;

use Botble\SeoHelper\Contracts\Entities\MetaCollectionContract;
use Botble\SeoHelper\Contracts\Helpers\MetaContract;
use Botble\SeoHelper\Contracts\RenderableContract;
use Botble\SeoHelper\Exceptions\InvalidArgumentException;
use Botble\SeoHelper\Helpers\Meta;
use Illuminate\Support\Collection;

abstract class MetaCollection extends Collection implements MetaCollectionContract
{
    /**
     * Meta tag prefix.
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Meta tag name property.
     *
     * @var string
     */
    protected $nameProperty = 'name';

    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Ignored tags, they have dedicated class.
     *
     * @var array
     */
    protected $ignored = [];

    /**
     * Set meta prefix name.
     *
     * @param string $prefix
     *
     * @return MetaCollection
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this->refresh();
    }

    /**
     * Add many meta tags.
     *
     * @param array $meta
     *
     * @return MetaCollection
     */
    public function addMany(array $meta)
    {
        foreach ($meta as $name => $content) {
            $this->add(compact('name', 'content'));
        }

        return $this;
    }

    /**
     * Add a meta to collection.
     *
     * @param Meta|array $item
     *
     * @return MetaCollection
     */
    public function add($item)
    {
        if (empty($item)) {
            return $this;
        }

        return $this->addMeta($item);
    }

    /**
     * Make a meta and add it to collection.
     *
     * @param array $meta
     * @return MetaCollection
     * @throws InvalidArgumentException
     */
    protected function addMeta(array $meta)
    {
        $meta = Meta::make($meta['name'], $meta['content'], $this->nameProperty, $this->prefix);

        $this->put($meta->key(), $meta);

        return $this;
    }

    /**
     * Remove a meta from the collection by key.
     *
     * @param array|string $names
     *
     * @return MetaCollection
     */
    public function remove($names)
    {
        $names = $this->prepareName($names);

        return $this->forget($names);
    }

    /**
     * Render the tag.
     *
     * @return string
     */
    public function render()
    {
        $output = $this->map(function (RenderableContract $meta) {
            return $meta->render();
        })->toArray();

        return implode(PHP_EOL, array_filter($output));
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

    /**
     * Check if meta is ignored.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function isIgnored($name)
    {
        return in_array($name, $this->ignored);
    }

    /**
     * Remove an item from the collection by key.
     *
     * @param string|array $keys
     *
     * @return MetaCollection
     */
    public function forget($keys)
    {
        foreach ((array)$keys as $key) {
            $this->offsetUnset($key);
        }

        return $this;
    }

    /**
     * Refresh meta collection items.
     *
     * @return MetaCollection
     */
    protected function refresh()
    {
        return $this->map(function (MetaContract $meta) {
            return $meta->setPrefix($this->prefix);
        });
    }

    /**
     * Prepare names.
     *
     * @param array|string $names
     *
     * @return array
     */
    protected function prepareName($names)
    {
        return array_map(function ($name) {
            return strtolower(trim($name));
        }, (array)$names);
    }
}
