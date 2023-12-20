<?php

namespace Botble\Media\Chunks;

use Botble\Media\Chunks\Exceptions\ChunkSaveException;

class FileMerger
{
    /**
     * @var bool|resource
     */
    protected $destinationFile;

    /**
     * FileMerger constructor.
     *
     * @param string $targetFile
     * @throws ChunkSaveException
     */
    public function __construct(string $targetFile)
    {
        // Open the target file
        if (! $this->destinationFile = @fopen($targetFile, 'ab')) {
            throw new ChunkSaveException('Failed to open output stream.', 102);
        }
    }

    /**
     * Appends given file.
     *
     * @param string $sourceFilePath
     * @return $this
     * @throws ChunkSaveException
     */
    public function appendFile(string $sourceFilePath): self
    {
        // Open the new uploaded chunk
        if (! $in = @fopen($sourceFilePath, 'rb')) {
            @fclose($this->destinationFile);

            throw new ChunkSaveException('Failed to open input stream', 101);
        }

        // Read and write in buffs
        while ($buff = fread($in, 4096)) {
            fwrite($this->destinationFile, $buff);
        }

        @fclose($in);

        return $this;
    }

    /**
     * Closes the connection to the file.
     */
    public function close(): void
    {
        @fclose($this->destinationFile);
    }
}
