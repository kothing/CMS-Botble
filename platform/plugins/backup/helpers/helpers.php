<?php

use Illuminate\Support\Facades\File;

if (! function_exists('get_backup_size')) {
    function get_backup_size(string $key): int
    {
        $size = 0;

        foreach (File::allFiles(storage_path('app/backup/' . $key)) as $file) {
            $size += $file->getSize();
        }

        return $size;
    }
}
