<?php

namespace Botble\SeoHelper\Helpers;

use Botble\Base\Facades\BaseHelper;
use Botble\SeoHelper\Contracts\Helpers\MetaContract;
use Botble\SeoHelper\Exceptions\InvalidArgumentException;
use Illuminate\Support\Str;

class Meta implements MetaContract
{
    /**
     * Meta prefix name.
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * The meta name property.
     *
     * @var string
     */
    protected $nameProperty = 'name';

    /**
     * Meta name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Meta content.
     *
     * @var string
     */
    protected $content = '';

    /**
     * Make Meta instance.
     *
     * @param string $name
     * @param string $content
     * @param string $prefix
     * @param string $propertyName
     * @throws InvalidArgumentException
     */
    public function __construct($name, $content, $propertyName = 'name', $prefix = '')
    {
        $this->setPrefix($prefix);
        $this->setName($name);
        $this->setContent($content);
        $this->setNameProperty($propertyName);
    }

    /**
     * Set the meta prefix name.
     *
     * @param string $prefix
     *
     * @return Meta
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Set the meta property name.
     *
     * @param string $nameProperty
     *
     * @return Meta
     * @throws InvalidArgumentException
     */
    public function setNameProperty($nameProperty)
    {
        $this->checkNameProperty($nameProperty);
        $this->nameProperty = $nameProperty;

        return $this;
    }

    /**
     * Check the name property.
     *
     * @param string $nameProperty
     *
     * @throws InvalidArgumentException
     */
    protected function checkNameProperty(&$nameProperty)
    {
        if (! is_string($nameProperty)) {
            throw new InvalidArgumentException(
                'The meta name property is must be a string value, ' . gettype($nameProperty) . ' is given.'
            );
        }

        $name = Str::slug($nameProperty);
        $allowed = ['charset', 'http-equiv', 'itemprop', 'name', 'property'];

        if (! in_array($name, $allowed)) {
            throw new InvalidArgumentException(
                'The meta name property [' . $name . '] is not supported, ' .
                'the allowed name properties are ["' . implode("', '", $allowed) . '"].'
            );
        }

        $nameProperty = $name;
    }

    /**
     * Make Meta instance.
     *
     * @param string $name
     * @param string $content
     * @param string $propertyName
     * @param string $prefix
     *
     * @return Meta
     * @throws InvalidArgumentException
     */
    public static function make($name, $content, $propertyName = 'name', $prefix = '')
    {
        return new self($name, $content, $propertyName, $prefix);
    }

    /**
     * Get the meta name.
     *
     * @return string
     */
    public function key()
    {
        return $this->name;
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
        if ($this->isLink()) {
            return $this->renderLink();
        }

        return $this->renderMeta();
    }

    /**
     * Check if meta is a link tag.
     *
     * @return bool
     */
    protected function isLink()
    {
        return in_array($this->name, [
            'alternate',
            'archives',
            'author',
            'canonical',
            'first',
            'help',
            'icon',
            'index',
            'last',
            'license',
            'next',
            'nofollow',
            'noreferrer',
            'pingback',
            'prefetch',
            'prev',
            'publisher',
        ]);
    }

    /**
     * Render the link tag.
     *
     * @return string
     */
    protected function renderLink()
    {
        return '<link rel="' . $this->getName(false) . '" href="' . $this->getContent() . '">';
    }

    /**
     * Get the meta name.
     *
     * @param bool $prefixed
     *
     * @return string
     */
    protected function getName(bool $prefixed = true)
    {
        $name = $this->name;

        if ($prefixed) {
            $name = $this->prefix . $name;
        }

        return $this->clean($name);
    }

    /**
     * Set the meta name.
     *
     * @param string $name
     *
     * @return Meta
     */
    protected function setName($name)
    {
        $name = trim(strip_tags((string)$name));
        $this->name = str_replace([' '], '-', $name);

        return $this;
    }

    /**
     * Clean all the inputs.
     *
     * @param string $value
     *
     * @return string
     */
    public function clean($value)
    {
        return BaseHelper::html(strip_tags((string)$value));
    }

    /**
     * Get the meta content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->clean($this->content);
    }

    /**
     * Set the meta content.
     *
     * @param string $content
     *
     * @return Meta
     */
    protected function setContent($content)
    {
        if (is_string($content)) {
            $this->content = trim($content);
        }

        return $this;
    }

    /**
     * Render the meta tag.
     *
     * @return string
     */
    protected function renderMeta()
    {
        $output = [];
        $output[] = $this->nameProperty . '="' . $this->getName() . '"';
        $output[] = 'content="' . $this->getContent() . '"';

        return '<meta ' . implode(' ', $output) . '>';
    }

    /**
     * Check if meta is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return ! empty($this->content);
    }
}
