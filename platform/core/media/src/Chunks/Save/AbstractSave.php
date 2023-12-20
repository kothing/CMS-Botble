<?php

namespace Botble\Media\Chunks\Save;

use Botble\Media\Chunks\Handler\AbstractHandler;
use Illuminate\Http\UploadedFile;

abstract class AbstractSave
{
    /**
     * @param UploadedFile $file the uploaded file (chunk file)
     * @param AbstractHandler $handler the handler that detected the correct save method
     */
    public function __construct(protected UploadedFile $file, protected AbstractHandler $handler)
    {
    }

    /**
     * Checks if the file upload is finished.
     */
    public function isFinished(): bool
    {
        return $this->isValid();
    }

    /**
     * Checks if the upload is valid.
     */
    public function isValid(): bool
    {
        return $this->file->isValid();
    }

    /**
     * Returns the error message.
     *
     * @return string|null
     */
    public function getErrorMessage(): string|null
    {
        return $this->file->getErrorMessage();
    }

    /**
     * Passes all the function into the file.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->getFile(), $name], $arguments);
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function handler(): AbstractHandler
    {
        return $this->handler;
    }
}
