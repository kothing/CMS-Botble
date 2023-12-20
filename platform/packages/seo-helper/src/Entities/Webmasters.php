<?php

namespace Botble\SeoHelper\Entities;

use Botble\SeoHelper\Contracts\Entities\WebmastersContract;

class Webmasters implements WebmastersContract
{
    protected $supported = [
        'google' => 'google-site-verification',
        'bing' => 'msvalidate.01',
        'alexa' => 'alexaVerifyID',
        'pinterest' => 'p:domain_verify',
        'yandex' => 'yandex-verification',
    ];

    protected MetaCollection $meta;

    /**
     * Create Webmasters instance.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Get the webmaster meta name.
     *
     * @param $webmaster
     * @return mixed|null
     */
    protected function getWebmasterName($webmaster)
    {
        if ($this->isSupported($webmaster)) {
            return $this->supported[$webmaster];
        }

        return null;
    }

    /**
     * Make Webmaster instance.
     *
     * @param array $webmasters
     *
     * @return Webmasters
     */
    public static function make(array $webmasters = [])
    {
        return new self();
    }

    /**
     * Add a webmaster to collection.
     *
     * @param string $webmaster
     * @param string $content
     *
     * @return Webmasters
     */
    public function add($webmaster, $content)
    {
        if (! empty($name = $this->getWebmasterName($webmaster))) {
            $this->meta->add(compact('name', 'content'));
        }

        return $this;
    }

    /**
     * Reset the webmaster collection.
     *
     * @return Webmasters
     */
    public function reset()
    {
        $this->meta = new MetaCollection();

        return $this;
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

    /**
     * Check if the webmaster is supported.
     *
     * @param string $webmaster
     *
     * @return bool
     */
    protected function isSupported($webmaster)
    {
        return array_key_exists($webmaster, $this->supported);
    }
}
