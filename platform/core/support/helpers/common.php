<?php

use Botble\Base\Facades\BaseHelper;
use Carbon\Carbon;

if (! function_exists('format_time')) {
    /**
     * @deprecated
     */
    function format_time(Carbon $timestamp, string $format = 'j M Y H:i'): string
    {
        return BaseHelper::formatTime($timestamp, $format);
    }
}

if (! function_exists('date_from_database')) {
    /**
     * @deprecated
     */
    function date_from_database(string $time, string $format = 'Y-m-d'): string
    {
        return BaseHelper::formatDate($time, $format);
    }
}

if (! function_exists('human_file_size')) {
    /**
     * @deprecated
     */
    function human_file_size(float $bytes, int $precision = 2): string
    {
        return BaseHelper::humanFilesize($bytes, $precision);
    }
}

if (! function_exists('get_file_data')) {
    /**
     * @deprecated
     */
    function get_file_data(string $file, bool $toArray = true): string|array|null
    {
        return BaseHelper::getFileData($file, $toArray);
    }
}

if (! function_exists('json_encode_prettify')) {
    /**
     * @deprecated
     */
    function json_encode_prettify(array $data): string
    {
        return BaseHelper::jsonEncodePrettify($data);
    }
}

if (! function_exists('save_file_data')) {
    /**
     * @deprecated
     */
    function save_file_data(string $path, array|string|null $data, bool $json = true): bool
    {
        return BaseHelper::saveFileData($path, $data, $json);
    }
}

if (! function_exists('scan_folder')) {
    /**
     * @deprecated
     */
    function scan_folder(string $path, array $ignoreFiles = []): array
    {
        return BaseHelper::scanFolder($path, $ignoreFiles);
    }
}
