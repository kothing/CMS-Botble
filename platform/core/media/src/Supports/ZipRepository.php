<?php

namespace Botble\Media\Supports;

use Exception;
use ZipArchive;

class ZipRepository implements ZipperInterface
{
    protected ZipArchive|null $archive;

    public function __construct($filePath, $create = false, $archive = null)
    {
        if (! class_exists('ZipArchive')) {
            throw new Exception('Error: Your PHP version is not compiled with zip support');
        }

        $this->archive = $archive ?: new ZipArchive();

        $res = $this->archive->open($filePath, ($create ? ZipArchive::CREATE : null));

        if ($res !== true) {
            throw new Exception('Error: Failed to open ' . $filePath . '! Error: ' . $this->getErrorMessage($res));
        }
    }

    protected function getErrorMessage($resultCode): string
    {
        return match ($resultCode) {
            ZipArchive::ER_EXISTS => 'ZipArchive::ER_EXISTS - File already exists.',
            ZipArchive::ER_INCONS => 'ZipArchive::ER_INCONS - Zip archive inconsistent.',
            ZipArchive::ER_MEMORY => 'ZipArchive::ER_MEMORY - Malloc failure.',
            ZipArchive::ER_NOENT => 'ZipArchive::ER_NOENT - No such file.',
            ZipArchive::ER_NOZIP => 'ZipArchive::ER_NOZIP - Not a zip archive.',
            ZipArchive::ER_OPEN => 'ZipArchive::ER_OPEN - Can\'t open file.',
            ZipArchive::ER_READ => 'ZipArchive::ER_READ - Read error.',
            ZipArchive::ER_SEEK => 'ZipArchive::ER_SEEK - Seek error.',
            default => 'An unknown error [' . $resultCode . '] has occurred.',
        };
    }

    public function addFile($pathToFile, $pathInArchive)
    {
        $this->archive->addFile($pathToFile, $pathInArchive);
    }

    public function addFromString($name, $content)
    {
        $this->archive->addFromString($name, $content);
    }

    public function close()
    {
        @$this->archive->close();
    }
}
