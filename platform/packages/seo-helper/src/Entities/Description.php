<?php

namespace Botble\SeoHelper\Entities;

use Botble\Base\Facades\BaseHelper;
use Botble\SeoHelper\Contracts\Entities\DescriptionContract;
use Botble\SeoHelper\Exceptions\InvalidArgumentException;
use Botble\SeoHelper\Helpers\Meta;
use Illuminate\Support\Str;

class Description implements DescriptionContract
{
    /**
     * The meta name.
     *
     * @var string
     */
    protected $name = 'description';

    /**
     * The meta content.
     *
     * @var string
     */
    protected $content = '';

    /**
     * The description max length.
     *
     * @var int
     */
    protected $max = 386;

    /**
     * Make Description instance.
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $this->set(theme_option('seo_description'));
        $this->setMax(config('packages.seo-helper.general.description.max', 386));
    }

    /**
     * Get raw description content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get description content.
     *
     * @return string
     */
    public function get()
    {
        return Str::limit($this->getContent(), $this->getMax());
    }

    /**
     * Set description content.
     *
     * @param string $content
     *
     * @return $this
     */
    public function set($content)
    {
        if ($content) {
            $this->content = trim(strip_tags(BaseHelper::cleanShortcodes((string)$content)));
        }

        return $this;
    }

    /**
     * Get description max length.
     *
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set description max length.
     *
     * @param int $max
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setMax($max)
    {
        $this->checkMax($max);

        $this->max = $max;

        return $this;
    }

    /**
     * Make a description instance.
     *
     * @param string $content
     * @param int $max
     */
    public static function make($content, $max = 386): self
    {
        return new self();
    }

    /**
     * Render the tag.
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function render()
    {
        if (! $this->hasContent()) {
            return '';
        }

        return Meta::make($this->name, $this->get())->render();
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
     * Check if description has content.
     *
     * @return bool
     */
    protected function hasContent()
    {
        return ! empty($this->get());
    }

    /**
     * Check title max length.
     *
     * @param int $max
     *
     * @throws InvalidArgumentException
     */
    protected function checkMax($max)
    {
        if (! is_int($max)) {
            throw new InvalidArgumentException(
                'The description maximum length must be integer.'
            );
        }

        if ($max <= 0) {
            throw new InvalidArgumentException(
                'The description maximum length must be greater 0.'
            );
        }
    }
}
