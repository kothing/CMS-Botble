<?php

namespace Botble\SeoHelper;

use Botble\SeoHelper\Contracts\Entities\TwitterCardContract;
use Botble\SeoHelper\Contracts\SeoTwitterContract;

class SeoTwitter implements SeoTwitterContract
{
    /**
     * The Twitter Card instance.
     *
     * @var TwitterCardContract
     */
    protected $card;

    /**
     * Make SeoTwitter instance.
     */
    public function __construct()
    {
        $this->setCard(new Entities\Twitter\Card());
    }

    /**
     * Set the Twitter Card instance.
     *
     * @param TwitterCardContract $card
     *
     * @return SeoTwitter
     */
    public function setCard(TwitterCardContract $card)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * Set the card type.
     *
     * @param string $type
     *
     * @return SeoTwitter
     */
    public function setType($type)
    {
        $this->card->setType($type);

        return $this;
    }

    /**
     * Set the card site.
     *
     * @param string $site
     *
     * @return SeoTwitter
     */
    public function setSite($site)
    {
        $this->card->setSite($site);

        return $this;
    }

    /**
     * Set the card title.
     *
     * @param string $title
     *
     * @return SeoTwitter
     */
    public function setTitle($title)
    {
        $this->card->setTitle($title);

        return $this;
    }

    /**
     * Set the card description.
     *
     * @param string $description
     *
     * @return SeoTwitter
     */
    public function setDescription($description)
    {
        $this->card->setDescription($description);

        return $this;
    }

    /**
     * Add image to the card.
     *
     * @param string $url
     *
     * @return SeoTwitter
     */
    public function addImage($url)
    {
        $this->card->addImage($url);

        return $this;
    }

    /**
     * Add many meta to the card.
     *
     * @param array $meta
     *
     * @return SeoTwitter
     */
    public function addMetas(array $meta)
    {
        $this->card->addMetas($meta);

        return $this;
    }

    /**
     * Add a meta to the Twitter Card.
     *
     * @param string $name
     * @param string $content
     *
     * @return SeoTwitter
     */
    public function addMeta($name, $content)
    {
        $this->card->addMeta($name, $content);

        return $this;
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
     * Render the tag.
     *
     * @return string
     */
    public function render()
    {
        return $this->card->render();
    }
}
