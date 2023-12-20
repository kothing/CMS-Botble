<?php

namespace Botble\Base\Supports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Intervention\Image\AbstractFont;
use Intervention\Image\AbstractShape;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use InvalidArgumentException;

class Avatar
{
    protected string|null $name = null;

    protected int $chars = 1;

    protected string $shape = 'circle';

    protected int $width = 250;

    protected int $height = 250;

    protected array $availableBackgrounds = [
        '#f44336',
        '#E91E63',
        '#9C27B0',
        '#673AB7',
        '#3F51B5',
        '#2196F3',
        '#03A9F4',
        '#00BCD4',
        '#009688',
        '#4CAF50',
        '#8BC34A',
        '#CDDC39',
        '#FFC107',
        '#FF9800',
        '#FF5722',
    ];

    protected array $availableForegrounds = [
        '#FFFFFF',
    ];

    protected int $fontSize = 152;

    protected int $borderSize = 0;

    protected string $borderColor = 'foreground';

    protected bool $ascii = false;

    protected bool $uppercase = false;

    protected Image $image;

    protected string $font;

    protected string $background = '#CCCCCC';

    protected string $foreground = '#FFFFFF';

    public function __construct()
    {
        $this->foreground = $this->getRandomElement($this->availableForegrounds, $this->foreground);
        $this->background = $this->getRandomElement($this->availableBackgrounds, $this->background);
        $this->font = core_path('base/public/fonts/Roboto-Bold.ttf');
    }

    public function setBackground(string $color): self
    {
        $this->background = $color;

        return $this;
    }

    public function setForeground(string $color): self
    {
        $this->foreground = $color;

        return $this;
    }

    public function setShape(string $shape): self
    {
        $this->shape = $shape;

        return $this;
    }

    protected function getRandomElement(array $array, string $default)
    {
        // Make it work for associative array
        $array = array_values($array);

        $name = $this->name;
        if ($name == null) {
            $name = chr(rand(65, 90));
        }

        if (count($array) == 0) {
            return $default;
        }

        $number = ord($name[0]);
        $index = 1;
        $charLength = strlen($name);
        while ($index < $charLength) {
            $number += ord($name[$index]);
            $index++;
        }

        return $array[$number % count($array)];
    }

    public function __toString()
    {
        return $this->toBase64();
    }

    public function toBase64(): string
    {
        $key = $this->cacheKey();
        if ($base64 = Cache::get($key)) {
            return $base64;
        }

        $this->buildAvatar();

        $base64 = (string)$this->image->encode('data-url');

        Cache::forever($key, $base64);

        return $base64;
    }

    protected function cacheKey(): string
    {
        $keys = [];
        $attributes = [
            'name',
            'shape',
            'chars',
            'font',
            'fontSize',
            'width',
            'height',
            'borderSize',
            'borderColor',
        ];

        foreach ($attributes as $attr) {
            $keys[] = $this->$attr;
        }

        return md5(implode('-', $keys));
    }

    public function buildAvatar(): self
    {
        $driver = 'gd';
        if (extension_loaded('imagick')) {
            $driver = 'imagick';
        }

        $manager = new ImageManager(['driver' => $driver]);
        $this->image = $manager->canvas($this->width, $this->height);

        $this->createShape();

        $this->image->text(
            $this->make($this->name, $this->chars, $this->uppercase, $this->ascii),
            $this->width / 2,
            $this->height / 2,
            function (AbstractFont $font) {
                $font->file($this->font);
                $font->size($this->fontSize);
                $font->color($this->foreground);
                $font->align('center');
                $font->valign('middle');
            }
        );

        return $this;
    }

    protected function createShape()
    {
        $method = 'create' . ucfirst($this->shape) . 'Shape';
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new InvalidArgumentException('Shape [' . $this->shape . '] currently not supported.');
    }

    public function make(string|array|null|object $name, int $length = 1, bool $uppercase = false, bool $ascii = false): string
    {
        $this->setName($name, $ascii);

        $words = collect(explode(' ', $this->name));

        // if name contains single word, use first N character
        if ($words->count() === 1) {
            $initial = (string)$words->first();

            if (strlen($this->name) >= $length) {
                $initial = Str::substr($this->name, 0, $length);
            }
        } else {
            // otherwise, use initial char from each word
            $initials = new Collection();
            $words->each(function ($word) use ($initials) {
                $initials->push(Str::substr($word, 0, 1));
            });

            $initial = $initials->slice(0, $length)->implode('');
        }

        if ($uppercase) {
            $initial = strtoupper($initial);
        }

        return $initial;
    }

    protected function setName(string|array|null|object $name, bool $ascii): void
    {
        if (is_array($name)) {
            throw new InvalidArgumentException(
                'Passed value cannot be an array'
            );
        } elseif (is_object($name) && ! method_exists($name, '__toString')) {
            throw new InvalidArgumentException(
                'Passed object must have a __toString method'
            );
        }

        if (filter_var($name, FILTER_VALIDATE_EMAIL)) {
            // turn bayu.hendra@gmail.com into "Bayu Hendra"
            $name = str_replace('.', ' ', Str::before($name, '@'));
        }

        if ($ascii) {
            $name = Str::ascii($name);
        }

        $this->name = $name;
    }

    public function create(string|null $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function save(string $path, int $quality = 90): Image
    {
        $this->buildAvatar();

        return $this->image->save($path, $quality);
    }

    protected function createCircleShape(): void
    {
        $this->image->circle(
            $this->width - $this->borderSize,
            intval($this->width / 2),
            intval($this->height / 2),
            function (AbstractShape $draw) {
                $draw->background($this->background);
                $draw->border($this->borderSize, $this->getBorderColor());
            }
        );
    }

    protected function getBorderColor(): string|null
    {
        if ($this->borderColor == 'foreground') {
            return $this->foreground;
        }

        if ($this->borderColor == 'background') {
            return $this->background;
        }

        return $this->borderColor;
    }

    protected function createSquareShape(): void
    {
        $edge = (int)ceil($this->borderSize / 2);
        $width = $this->width - $edge;
        $height = $this->height - $edge;

        $this->image->rectangle(
            $edge,
            $edge,
            $width,
            $height,
            function (AbstractShape $draw) {
                $draw->background($this->background);
                $draw->border($this->borderSize, $this->getBorderColor());
            }
        );
    }
}
