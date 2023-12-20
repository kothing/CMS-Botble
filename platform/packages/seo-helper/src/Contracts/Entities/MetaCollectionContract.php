<?php

namespace Botble\SeoHelper\Contracts\Entities;

use Botble\SeoHelper\Contracts\RenderableContract;

interface MetaCollectionContract extends RenderableContract
{
    /**
     * Add a meta to collection.
     *
     * @param array $item
     * @return $this
     */
    public function add($item);

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
}
