<?php

namespace Botble\SeoHelper\Entities\Twitter;

use Botble\SeoHelper\Contracts\Entities\MetaCollectionContract;
use Botble\SeoHelper\Contracts\Entities\MiscTagsContract;
use Botble\SeoHelper\Contracts\Entities\TwitterCardContract;
use Botble\SeoHelper\Exceptions\InvalidTwitterCardException;
use Illuminate\Support\Str;

class Card implements TwitterCardContract
{
    /**
     * Card type.
     *
     * @var string
     */
    protected $type;

    /**
     * Card meta collection.
     *
     * @var MetaCollectionContract|MiscTagsContract
     */
    protected $meta;

    /**
     * Card images.
     *
     * @var array
     */
    protected $images = [];

    /**
     * Make the twitter card instance.
     */
    public function __construct()
    {
        $this->meta = new MetaCollection();
    }

    /**
     * Set the card type.
     *
     * @param string $type
     *
     * @return Card
     * @throws InvalidTwitterCardException
     */
    public function setType($type)
    {
        if (empty($type)) {
            return $this;
        }

        $this->checkType($type);
        $this->type = $type;

        return $this->addMeta('card', $type);
    }

    /**
     * Set card site.
     *
     * @param string $site
     *
     * @return Card
     */
    public function setSite($site)
    {
        if (empty($site)) {
            return $this;
        }

        $this->checkSite($site);

        return $this->addMeta('site', $site);
    }

    /**
     * Set card title.
     *
     * @param string $title
     *
     * @return Card
     */
    public function setTitle($title)
    {
        return $this->addMeta('title', $title);
    }

    /**
     * Set card description.
     *
     * @param string $description
     *
     * @return Card
     */
    public function setDescription($description)
    {
        return $this->addMeta('description', $description);
    }

    /**
     * Add image to the card.
     *
     * @param string $url
     *
     * @return Card
     */
    public function addImage($url)
    {
        if (count($this->images) < 4) {
            $this->images[] = $url;
        }

        return $this;
    }

    /**
     * Add many meta to the card.
     *
     * @param array $meta
     *
     * @return Card
     */
    public function addMetas(array $meta)
    {
        $this->meta->addMany($meta);

        return $this;
    }

    /**
     * Add a meta to the card.
     *
     * @param string $name
     * @param string $content
     *
     * @return Card
     */
    public function addMeta($name, $content)
    {
        $this->meta->add(compact('name', 'content'));

        return $this;
    }

    /**
     * Get all supported card types.
     *
     * @return array
     */
    public function types()
    {
        return [
            static::TYPE_APP,
            static::TYPE_GALLERY,
            static::TYPE_PHOTO,
            static::TYPE_PLAYER,
            static::TYPE_PRODUCT,
            static::TYPE_SUMMARY,
            static::TYPE_SUMMARY_LARGE_IMAGE,
        ];
    }

    /**
     * Render card images.
     */
    protected function loadImages()
    {
        if (count($this->images) == 1) {
            $this->addMeta('image', $this->images[0]);

            return;
        }

        foreach ($this->images as $number => $url) {
            $this->addMeta('image{' . $number . '}', $url);
        }
    }

    /**
     * Reset the card.
     *
     * @return Card
     */
    public function reset()
    {
        $this->meta->reset();
        $this->images = [];

        return $this;
    }

    /**
     * Render the twitter card.
     *
     * @return string
     */
    public function render()
    {
        if (! empty($this->images)) {
            $this->loadImages();
        }

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

    /**
     * Check the card type.
     *
     * @param string $type
     *
     * @throws InvalidTwitterCardException
     */
    protected function checkType(&$type)
    {
        if (! is_string($type)) {
            throw new InvalidTwitterCardException(
                'The Twitter card type must be a string value, [' . gettype($type) . '] was given.'
            );
        }

        $type = strtolower(trim($type));

        if (! in_array($type, $this->types())) {
            throw new InvalidTwitterCardException('The Twitter card type [' . $type . '] is not supported.');
        }
    }

    /**
     * Check the card site.
     *
     * @param string $site
     */
    protected function checkSite(&$site)
    {
        $site = $this->prepareUsername($site);
    }

    /**
     * Prepare username.
     *
     * @param string $username
     *
     * @return string
     */
    protected function prepareUsername($username)
    {
        if (! Str::startsWith($username, '@')) {
            $username = '@' . $username;
        }

        return $username;
    }
}
