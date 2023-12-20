<?php

namespace Botble\Backup\Supports\MySql;

use Exception;

abstract class CompressManagerFactory
{
    /**
     * @param string $compress
     *
     * @return CompressNone
     *
     * @throws Exception
     */
    public static function create($compress)
    {
        $compress = ucfirst(strtolower($compress));

        $method = __NAMESPACE__ . '\\' . 'Compress' . $compress;

        return new $method();
    }
}
