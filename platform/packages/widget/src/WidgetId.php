<?php

namespace Botble\Widget;

class WidgetId
{
    protected static int $id = 0;

    public static function get(): int
    {
        return self::$id;
    }

    public static function set(int $id): void
    {
        self::$id = $id;
    }

    public static function increment(): void
    {
        self::$id++;
    }

    public static function reset(): void
    {
        self::$id = 0;
    }
}
