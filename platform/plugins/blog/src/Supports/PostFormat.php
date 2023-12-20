<?php

namespace Botble\Blog\Supports;

class PostFormat
{
    protected static array $formats = [
        '' => [
            'key' => '',
            'icon' => null,
            'name' => 'Default',
        ],
    ];

    public static function registerPostFormat(array $formats = []): void
    {
        foreach ($formats as $key => $format) {
            self::$formats[$key] = $format;
        }
    }

    public static function getPostFormats(bool $toArray = false): array
    {
        if ($toArray) {
            $results = [];
            foreach (self::$formats as $key => $item) {
                $results[$key] = [
                    $key,
                    $item['name'],
                ];
            }

            return $results;
        }

        return self::$formats;
    }
}
