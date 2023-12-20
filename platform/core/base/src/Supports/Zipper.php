<?php

namespace Botble\Base\Supports;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\PclZip as Zip;
use Illuminate\Support\Facades\File;
use ZipArchive;

class Zipper
{
    protected bool $isZipArchiveEnabled;

    public function __construct()
    {
        $this->isZipArchiveEnabled = class_exists('ZipArchive', false);
    }

    public function compress(string $src, string $destination): bool
    {
        $this->ensureDirectoryExists($destination);

        if ($this->isZipArchiveEnabled) {
            $zip = new ZipArchive();

            if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) && File::isFile($src)) {
                $zip->addFile($src, File::basename($src));
                $zip->close();

                return true;
            }
        } else {
            $zip = new Zip($destination);
        }

        $arrSource = explode(DIRECTORY_SEPARATOR, $src);
        $pathLength = strlen(implode(DIRECTORY_SEPARATOR, $arrSource) . DIRECTORY_SEPARATOR);

        self::recurseZip($src, $zip, $pathLength);

        if ($this->isZipArchiveEnabled) {
            $zip->close();
        }

        return true;
    }

    public function extract(string $src, string $destination): bool
    {
        $this->ensureDirectoryExists($destination);

        if ($this->isZipArchiveEnabled) {
            $zip = new ZipArchive();

            if ($zip->open($src) === true) {
                $zip->extractTo($destination);
                $zip->close();

                return true;
            }

            return false;
        }

        $archive = new Zip($src);
        $archive->extract(PCLZIP_OPT_PATH, $destination);

        return true;
    }

    public function recurseZip(string $src, $zip, int $pathLength): void
    {
        foreach (BaseHelper::scanFolder($src) as $file) {
            $filePath = $src . DIRECTORY_SEPARATOR . $file;

            if (File::isDirectory($filePath)) {
                $this->recurseZip($filePath, $zip, $pathLength);
            } elseif (class_exists('ZipArchive', false)) {
                $zip->addFile($filePath, substr($filePath, $pathLength));
            } else {
                $zip->add($filePath, PCLZIP_OPT_REMOVE_PATH, substr($filePath, $pathLength));
            }
        }
    }

    protected function ensureDirectoryExists(string $path): void
    {
        File::ensureDirectoryExists(File::isFile($path) || File::extension($path) ? File::dirname($path) : $path);
    }
}
