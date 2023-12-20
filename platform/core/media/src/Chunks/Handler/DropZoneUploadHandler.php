<?php

namespace Botble\Media\Chunks\Handler;

use Botble\Media\Chunks\Save\ParallelSave;
use Illuminate\Http\Request;

class DropZoneUploadHandler extends AbstractHandler
{
    public const CHUNK_UUID_INDEX = 'dzuuid';
    public const CHUNK_INDEX = 'dzchunkindex';
    public const CHUNK_TOTAL_INDEX = 'dztotalchunkcount';

    /**
     * The DropZone file uuid.
     *
     * @var string|null
     */
    protected $fileUuid = null;

    /**
     * The current chunk progress.
     *
     * @var int
     */
    protected $currentChunk = 0;

    /**
     * The total of chunks.
     *
     * @var int
     */
    protected $chunksTotal = 0;

    public function __construct(Request $request, $file)
    {
        parent::__construct($request, $file);

        $this->currentChunk = intval($request->input(self::CHUNK_INDEX, 0)) + 1;
        $this->chunksTotal = intval($request->input(self::CHUNK_TOTAL_INDEX, 1));
        $this->fileUuid = $request->input(self::CHUNK_UUID_INDEX);
    }

    public function getChunkFileName()
    {
        return $this->createChunkFileName($this->fileUuid, $this->currentChunk);
    }

    public function startSaving($chunkStorage)
    {
        // Build the parallel save
        return new ParallelSave($this->file, $this, $chunkStorage);
    }

    public function isFirstChunk(): bool
    {
        return 1 == $this->currentChunk;
    }

    public function isLastChunk(): bool
    {
        // the bytes start from zero, remove 1 byte from total
        return $this->currentChunk == $this->chunksTotal;
    }

    public function isChunkedUpload(): bool
    {
        return $this->chunksTotal > 1;
    }

    public function getPercentageDone()
    {
        if (! $this->chunksTotal) {
            return 100;
        }

        return ceil($this->currentChunk / $this->chunksTotal * 100);
    }
}
