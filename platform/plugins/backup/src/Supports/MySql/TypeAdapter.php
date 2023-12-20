<?php

namespace Botble\Backup\Supports\MySql;

/**
 * Enum with all available TypeAdapter implementations.
 */
abstract class TypeAdapter
{
    public static $enums = [
        'Sqlite',
        'Mysql',
    ];

    /**
     * @param string $compress
     *
     * @return bool
     */
    public static function isValid($compress)
    {
        return in_array($compress, self::$enums);
    }
}
