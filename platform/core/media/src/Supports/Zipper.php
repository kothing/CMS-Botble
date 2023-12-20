<?php

namespace Botble\Media\Supports;

use Exception;
use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use RuntimeException;

/**
 * This Zipper class is a wrapper around the ZipArchive methods with some handy functions
 *
 * Class Zipper
 */
class Zipper
{
    /**
     * @var string Represents the current location in the archive
     */
    protected $currentFolder = '';

    /**
     * @var Filesystem Handler to the file system
     */
    protected $file;

    /**
     * @var ZipperInterface Handler to the archive
     */
    protected $repository;

    /**
     * @var string The path to the current zip file
     */
    protected $filePath;

    /**
     * Constructor
     *
     * @param Filesystem|null $fs
     */
    public function __construct(Filesystem $fs = null)
    {
        $this->file = $fs ?: new Filesystem();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (is_object($this->repository)) {
            $this->repository->close();
        }
    }

    /**
     * Create a new zip Archive if the file does not exist
     * opens a zip archive if the file exists
     *
     * @param string $pathToFile The file to open
     * @return $this Zipper instance
     * @throws Exception
     * @throws InvalidArgumentException
     *
     * @throws RuntimeException
     */
    public function make($pathToFile)
    {
        $new = $this->createArchiveFile($pathToFile);

        $this->repository = new ZipRepository($pathToFile, $new);

        $this->filePath = $pathToFile;

        return $this;
    }

    /**
     * @param string $pathToZip
     *
     * @return bool
     * @throws Exception
     */
    protected function createArchiveFile($pathToZip)
    {
        if (! $this->file->exists($pathToZip)) {
            $dirname = dirname($pathToZip);
            if (! $this->file->exists($dirname) && ! $this->file->makeDirectory($dirname, 0755, true)) {
                throw new RuntimeException('Failed to create folder');
            } elseif (! $this->file->isWritable($dirname)) {
                throw new Exception(sprintf('The path "%s" is not writeable', $pathToZip));
            }

            return true;
        }

        return false;
    }

    /**
     * Add one or multiple files to the zip.
     *
     * @param array|string $pathToAdd An array or string of files and folders to add
     * @param null|mixed $fileName
     *
     * @return $this Zipper instance
     */
    public function add($pathToAdd, $fileName = null)
    {
        if (is_array($pathToAdd)) {
            foreach ($pathToAdd as $key => $dir) {
                if (! is_int($key)) {
                    $this->add($dir, $key);
                } else {
                    $this->add($dir);
                }
            }
        } elseif ($this->file->isFile($pathToAdd)) {
            $this->addFile($pathToAdd, $fileName);
        } else {
            $this->addDir($pathToAdd);
        }

        return $this;
    }

    /**
     * Add the file to the zip
     *
     * @param string $pathToAdd
     * @param string $fileName
     */
    protected function addFile($pathToAdd, $fileName = null)
    {
        if (! $fileName) {
            $info = pathinfo($pathToAdd);
            $fileName = isset($info['extension']) ?
                $info['filename'] . '.' . $info['extension'] :
                $info['filename'];
        }

        $this->repository->addFile($pathToAdd, $this->getInternalPath() . $fileName);
    }

    /**
     * Gets the path to the internal folder
     *
     * @return string
     */
    public function getInternalPath()
    {
        return empty($this->currentFolder) ? '' : $this->currentFolder . '/';
    }

    /**
     * @param string $pathToDir
     */
    protected function addDir($pathToDir)
    {
        // First go over the files in this directory and add them to the repository.
        foreach ($this->file->files($pathToDir) as $file) {
            $this->addFile($pathToDir . '/' . basename($file));
        }

        // Now let's visit the subdirectories and add them, too.
        foreach ($this->file->directories($pathToDir) as $dir) {
            $oldFolder = $this->currentFolder;
            $this->currentFolder = empty($this->currentFolder) ? basename($dir) : $this->currentFolder . '/' . basename($dir);
            $this->addDir($pathToDir . '/' . basename($dir));
            $this->currentFolder = $oldFolder;
        }
    }

    /**
     * Add a file to the zip using its contents
     *
     * @param string $filename The name of the file to create
     * @param string $content The file contents
     *
     * @return $this Zipper instance
     */
    public function addString($filename, $content)
    {
        $this->repository->addFromString($this->getInternalPath() . $filename, $content);

        return $this;
    }

    /**
     * Closes the zip file and frees all handles
     */
    public function close()
    {
        if (null !== $this->repository) {
            $this->repository->close();
        }

        $this->filePath = '';
    }
}
