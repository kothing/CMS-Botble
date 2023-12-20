<?php

namespace Botble\Media\Chunks\Receiver;

use Botble\Media\Chunks\Exceptions\UploadFailedException;
use Botble\Media\Chunks\Handler\AbstractHandler;
use Botble\Media\Chunks\Save\AbstractSave;
use Botble\Media\Chunks\Storage\ChunkStorage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class FileReceiver
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var UploadedFile|null
     */
    protected $file;

    /**
     * The handler that detects what upload process is being used.
     *
     * @var AbstractHandler
     */
    protected $handler = null;

    /**
     * The chunk storage.
     *
     * @var ChunkStorage
     */
    protected $chunkStorage;

    /**
     * The file receiver for the given file index.
     *
     * @param string|UploadedFile $fileIndexOrFile the desired file index to use in request or the final UploadedFile
     * @param Request $request the current request
     * @param string $handlerClass the handler class name for detecting the file upload
     * @param ChunkStorage|null $chunkStorage the chunk storage, on null will use the instance from app container
     *
     * @throws UploadFailedException
     */
    public function __construct($fileIndexOrFile, Request $request, $handlerClass, $chunkStorage = null)
    {
        $this->request = $request;
        $this->file = is_object($fileIndexOrFile) ? $fileIndexOrFile : $request->file($fileIndexOrFile);
        $this->chunkStorage = $chunkStorage === null ? ChunkStorage::storage() : $chunkStorage;

        if ($this->isUploaded()) {
            if (! $this->file->isValid()) {
                throw new UploadFailedException($this->file->getErrorMessage());
            }

            $this->handler = new $handlerClass($this->request, $this->file);
        }
    }

    /**
     * Checks if the file was uploaded.
     *
     * @return bool
     */
    public function isUploaded(): bool
    {
        return is_object($this->file) && UPLOAD_ERR_NO_FILE !== $this->file->getError();
    }

    /**
     * Tries to handle the upload request. If the file is not uploaded, returns false. If the file
     * is present in the request, it will create the save object.
     *
     * If the file in the request is chunk, it will create the `ChunkSave` object
     * which doesn't do anything at this moment.
     *
     * @return bool|AbstractSave
     */
    public function receive()
    {
        if (false === is_object($this->handler)) {
            return false;
        }

        return $this->handler->startSaving($this->chunkStorage);
    }
}
