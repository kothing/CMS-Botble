<?php

namespace Botble\Media\Chunks\Storage;

use Botble\Media\Chunks\ChunkFile;
use Botble\Media\Facades\RvMedia;
use Carbon\Carbon;
use Closure;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Local\LocalFilesystemAdapter;
use RuntimeException;
use Throwable;

class ChunkStorage
{
    public const CHUNK_EXTENSION = 'part';

    protected array $config;

    /**
     * The disk that holds the chunk files.
     *
     * @var FilesystemAdapter
     */
    protected $disk;

    /**
     * @var LocalFilesystemAdapter|FilesystemAdapter
     */
    protected $diskAdapter;

    /**
     * Is provided disk a local drive.
     */
    protected bool $isLocalDisk;

    /**
     * ChunkStorage constructor.
     */
    public function __construct()
    {
        $this->config = RvMedia::getConfig('chunk');

        // Cache the storage path
        $this->disk = Storage::disk($this->config['storage']['disk']);

        $driver = $this->disk;

        // Try to get the adapter
        if (! method_exists($driver, 'getAdapter')) {
            throw new RuntimeException('FileSystem driver must have an adapter implemented');
        }

        // Get the disk adapter
        // @phpstan-ignore-next-line
        $this->diskAdapter = $driver->getAdapter();

        // Check if its local adapter
        $this->isLocalDisk = $this->diskAdapter instanceof LocalFilesystemAdapter;
    }

    /**
     * @return FilesystemAdapter
     */
    public function disk(): FilesystemAdapter
    {
        return $this->disk;
    }

    /**
     * Returns the application instance of the chunk storage.
     */
    public static function storage(): self
    {
        return app(self::class);
    }

    /**
     * The current path for chunks directory.
     *
     * @return string
     *
     * @throws RuntimeException when the adapter is not local
     */
    public function getDiskPathPrefix(): string
    {
        if ($this->isLocalDisk) {
            return $this->disk->path('');
        }

        throw new RuntimeException('The full path is not supported on current disk - local adapter supported only');
    }

    /**
     * Returns the old chunk files.
     *
     * @return Collection<ChunkFile> collection of a ChunkFile objects
     */
    public function oldChunkFiles(): Collection
    {
        $files = $this->files();
        // If there are no files, lets return the empty collection
        if ($files->isEmpty()) {
            return $files;
        }

        // Build the timestamp
        $timeToCheck = strtotime($this->config['clear']['timestamp']);
        $collection = new Collection();

        // Filter the collection with files that are not correct chunk file
        // Loop all current files and filter them by the time
        $files->each(function ($file) use ($timeToCheck, $collection) {
            // get the last modified time to check if the chunk is not new
            try {
                $modified = $this->disk()->lastModified($file);
            } catch (Throwable) {
                $modified = Carbon::now()->getTimestamp();
            }

            // Delete only old chunk
            if ($modified < $timeToCheck) {
                $collection->push(new ChunkFile($file, $modified, $this));
            }
        });

        return $collection;
    }

    /**
     * Returns an array of files in the chunk's directory.
     *
     * @param Closure|null $rejectClosure
     * @return Collection
     * @see FilesystemAdapter::files()
     */
    public function files(?Closure $rejectClosure = null): Collection
    {
        // We need to filter files we don't support, lets use the collection
        $filesCollection = new Collection($this->disk->files($this->directory(), false));

        return $filesCollection->reject(function ($file) use ($rejectClosure) {
            // Ensure the file ends with allowed extension
            $shouldReject = ! preg_match('/.' . self::CHUNK_EXTENSION . '$/', $file);
            if ($shouldReject) {
                return true;
            }

            if (is_callable($rejectClosure)) {
                return $rejectClosure($file);
            }

            return false;
        });
    }

    /**
     * The current chunk's directory.
     */
    public function directory(): string
    {
        return $this->config['storage']['chunks'] . '/';
    }
}
