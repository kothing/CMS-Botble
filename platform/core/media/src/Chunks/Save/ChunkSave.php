<?php

namespace Botble\Media\Chunks\Save;

use Botble\Media\Chunks\Exceptions\ChunkSaveException;
use Botble\Media\Chunks\FileMerger;
use Botble\Media\Chunks\Handler\AbstractHandler;
use Botble\Media\Chunks\Storage\ChunkStorage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;

class ChunkSave extends AbstractSave
{
    /**
     * Is this the final chunk?
     *
     * @var bool
     */
    protected $isLastChunk;

    /**
     * What is the chunk file name.
     *
     * @var string
     */
    protected $chunkFileName;

    /**
     * The chunk file path.
     *
     * @var string
     */
    protected $chunkFullFilePath = null;

    /**
     * @var UploadedFile|null
     */
    protected $fullChunkFile;

    protected ChunkStorage $chunkStorage;

    public function __construct(UploadedFile $file, AbstractHandler $handler, $chunkStorage)
    {
        parent::__construct($file, $handler);
        $this->chunkStorage = $chunkStorage;

        $this->isLastChunk = $handler->isLastChunk();
        $this->chunkFileName = $handler->getChunkFileName();

        // build the full disk path
        $this->chunkFullFilePath = $this->getChunkFilePath(true);

        $this->handleChunk();
    }

    /**
     * Returns the chunk file path in the current disk instance.
     */
    public function getChunkFilePath(bool $absolutePath = false): string
    {
        return $this->getChunkDirectory($absolutePath) . $this->chunkFileName;
    }

    /**
     * Returns the folder for the chunks in the storage path on current disk instance.
     */
    public function getChunkDirectory(bool $absolutePath = false): string
    {
        $paths = [];

        if ($absolutePath) {
            $paths[] = $this->chunkStorage()->getDiskPathPrefix();
        }

        $paths[] = $this->chunkStorage()->directory();

        return implode('', $paths);
    }

    /**
     * Returns the current chunk storage.
     */
    public function chunkStorage(): ChunkStorage
    {
        return $this->chunkStorage;
    }

    /**
     * Appends the new uploaded data to the final file.*
     * @throws ChunkSaveException
     */
    protected function handleChunk()
    {
        // prepare the folder and file path
        $this->createChunksFolderIfNeeded();
        $file = $this->getChunkFilePath();

        $this->handleChunkFile($file)
            ->tryToBuildFullFileFromChunks();
    }

    /**
     * Creates the chunks folder if it doesn't exist. Uses recursive create.
     */
    protected function createChunksFolderIfNeeded()
    {
        $path = $this->getChunkDirectory(true);

        // creates the chunks dir
        if (! file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    /**
     * Checks if the current chunk is last.
     */
    protected function tryToBuildFullFileFromChunks(): self
    {
        // Build the last file because of the last chunk
        if ($this->isLastChunk) {
            $this->buildFullFileFromChunks();
        }

        return $this;
    }

    /**
     * Builds the final file.
     */
    protected function buildFullFileFromChunks()
    {
        // Try to get local path
        $finalPath = $this->getChunkFullFilePath();

        // Build the new UploadedFile
        $this->fullChunkFile = $this->createFullChunkFile($finalPath);
    }

    /**
     * Returns the full file path.
     */
    public function getChunkFullFilePath(): string|null
    {
        return $this->chunkFullFilePath;
    }

    protected function createFullChunkFile(string|null $finalPath): UploadedFile
    {
        return new UploadedFile(
            $finalPath,
            $this->file->getClientOriginalName(),
            $this->file->getClientMimeType(),
            $this->file->getError(),
            // we must pass the true as test to force the upload file
            // to use a standard copy method, not move uploaded file
            true
        );
    }

    /**
     * @param $file
     * @return $this
     * @throws ChunkSaveException
     */
    protected function handleChunkFile($file): self
    {
        // delete the old chunk
        if ($this->handler()->isFirstChunk() && $this->chunkDisk()->exists($file)) {
            $this->chunkDisk()->delete($file);
        }

        // Append the data to the file
        (new FileMerger($this->getChunkFullFilePath()))
            ->appendFile($this->file->getPathname())
            ->close();

        return $this;
    }

    /**
     * Returns the disk adapter for the chunk.
     */
    public function chunkDisk(): FilesystemAdapter
    {
        return $this->chunkStorage()->disk();
    }

    public function isFinished(): bool
    {
        return parent::isFinished() && $this->isLastChunk;
    }

    public function getFile(): UploadedFile
    {
        if ($this->isLastChunk) {
            return $this->fullChunkFile;
        }

        return parent::getFile();
    }
}
