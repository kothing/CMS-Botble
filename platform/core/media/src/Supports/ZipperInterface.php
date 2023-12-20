<?php

namespace Botble\Media\Supports;

/**
 * RepositoryInterface that needs to be implemented by every Repository
 *
 * Class RepositoryInterface
 */
interface ZipperInterface
{
    /**
     * Construct with a given path
     *
     * @param string $filePath
     * @param bool $new
     * @param $archiveImplementation
     */
    public function __construct($filePath, $new = false, $archiveImplementation = null);

    /**
     * Add a file to the opened Archive
     *
     * @param string $pathToFile
     * @param string $pathInArchive
     */
    public function addFile($pathToFile, $pathInArchive);

    /**
     * Add a file to the opened Archive using its contents
     *
     * @param string $name
     * @param string $content
     */
    public function addFromString($name, $content);

    /**
     * Closes the archive and saves it
     */
    public function close();
}
