<?php

namespace Botble\Media\Chunks;

use Botble\Media\Chunks\Storage\ChunkStorage;

class ChunkFile
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var int
     */
    protected $modifiedTime;

    /**
     * The chunk storage.
     *
     * @var ChunkStorage
     */
    protected $storage;

    /**
     * Creates the chunk file.
     *
     * @param string $path
     * @param int $modifiedTime
     * @param ChunkStorage $storage
     */
    public function __construct($path, $modifiedTime, $storage)
    {
        $this->path = $path;
        $this->modifiedTime = $modifiedTime;
        $this->storage = $storage;
    }

    /**
     * @return string
     */
    public function getAbsolutePath(): string
    {
        return $this->storage->disk()->path($this->path ?: '');
    }

    /**
     * Moves the chunk file to given relative path (within the disk).
     */
    public function move(string $pathTo): bool
    {
        return $this->storage->disk()->move($this->path, $pathTo);
    }

    /**
     * Deletes the chunk file.
     */
    public function delete(): bool
    {
        return $this->storage->disk()->delete($this->path);
    }

    public function __toString()
    {
        return sprintf('ChunkFile %s uploaded at %s', $this->getPath(), date('Y-m-d H:i:s', $this->getModifiedTime()));
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getModifiedTime(): int
    {
        return $this->modifiedTime;
    }
}
