<?php

namespace Botble\Media\Chunks\Save;

use Botble\Media\Chunks\ChunkFile;
use Botble\Media\Chunks\Exceptions\ChunkSaveException;
use Botble\Media\Chunks\Exceptions\MissingChunkFilesException;
use Botble\Media\Chunks\FileMerger;
use Botble\Media\Chunks\Handler\AbstractHandler;
use Botble\Media\Chunks\Storage\ChunkStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ParallelSave extends ChunkSave
{
    /**
     * Stored on construct - the file is moved and isValid will return false.
     */
    protected bool $isFileValid;

    public function __construct(UploadedFile $file, AbstractHandler $handler, ChunkStorage $chunkStorage)
    {
        // Get current file validation - the file instance is changed
        $this->isFileValid = $file->isValid();

        // Handle the file upload
        parent::__construct($file, $handler, $chunkStorage);
    }

    public function isValid(): bool
    {
        return $this->isFileValid;
    }

    protected function handleChunkFile($file): ChunkSave
    {
        // Move the uploaded file to chunk folder
        $this->file->move($this->getChunkDirectory(true), $this->chunkFileName);

        return $this;
    }

    protected function tryToBuildFullFileFromChunks(): ChunkSave
    {
        return parent::tryToBuildFullFileFromChunks();
    }

    protected function getSavedChunksFiles(): Collection
    {
        $chunkFileName = preg_replace(
            '/\\.[\\d]+\\.' . ChunkStorage::CHUNK_EXTENSION . '$/',
            '',
            $this->handler()->getChunkFileName()
        );

        return $this->chunkStorage->files(function ($file) use ($chunkFileName) {
            return false === Str::contains($file, $chunkFileName);
        });
    }

    /**
     * @throws ChunkSaveException
     * @throws MissingChunkFilesException
     */
    protected function buildFullFileFromChunks()
    {
        $chunkFiles = $this->getSavedChunksFiles()->all();

        if (0 === count($chunkFiles)) {
            throw new MissingChunkFilesException();
        }

        // Sort the chunk order
        natcasesort($chunkFiles);

        // Get chunk files that matches the current chunk file name, also sort the chunk files.
        $finalFilePath = $this->getChunkDirectory(true) . './' . $this->handler()->createChunkFileName();
        // Delete the file if exists
        if (file_exists($finalFilePath)) {
            File::delete($finalFilePath);
        }

        $fileMerger = new FileMerger($finalFilePath);

        // Append each chunk file
        foreach ($chunkFiles as $filePath) {
            // Build the chunk file
            $chunkFile = new ChunkFile($filePath, 0, $this->chunkStorage());

            // Append the data
            $fileMerger->appendFile($chunkFile->getAbsolutePath());

            // Delete the chunk file
            $chunkFile->delete();
        }

        $fileMerger->close();

        // Build the chunk file instance
        $this->fullChunkFile = $this->createFullChunkFile($finalFilePath);
    }
}
