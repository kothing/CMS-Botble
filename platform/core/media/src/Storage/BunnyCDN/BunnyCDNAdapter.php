<?php

namespace Botble\Media\Storage\BunnyCDN;

use Exception;
use League\Flysystem\CalculateChecksumFromStream;
use League\Flysystem\ChecksumProvider;
use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\DirectoryListing;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use League\Flysystem\InvalidVisibilityProvided;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UrlGeneration\PublicUrlGenerator;
use League\Flysystem\Visibility;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use RuntimeException;
use TypeError;

class BunnyCDNAdapter implements FilesystemAdapter, PublicUrlGenerator, ChecksumProvider
{
    use CalculateChecksumFromStream;

    public function __construct(protected BunnyCDNClient $client, protected string $pullZoneURL = '')
    {
        if (func_num_args() > 2 && (string)func_get_arg(2) !== '') {
            throw new RuntimeException('PrefixPath is no longer supported directly. Use PathPrefixedAdapter instead: https://flysystem.thephpleague.com/docs/adapter/path-prefixing/');
        }
    }

    public function copy($source, $destination, Config $config): void
    {
        try {
            $this->write($destination, $this->read($source), new Config());
        } catch (UnableToReadFile|UnableToWriteFile $exception) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $exception);
        }
    }

    public function write($path, $contents, Config $config): void
    {
        try {
            $this->client->upload($path, $contents);
        } catch (Exceptions\BunnyCDNException $exception) {
            throw UnableToWriteFile::atLocation($path, $exception->getMessage());
        }
    }

    public function read(string $path): string
    {
        try {
            return $this->client->download($path);
        } catch (Exceptions\BunnyCDNException $exception) {
            throw UnableToReadFile::fromLocation($path, $exception->getMessage());
        }
    }

    public function listContents(string $path, bool $deep): iterable
    {
        try {
            $entries = $this->client->list($path);
        } catch (Exceptions\BunnyCDNException $exception) {
            throw UnableToRetrieveMetadata::create($path, 'folder', $exception->getMessage());
        }

        foreach ($entries as $item) {
            $content = $this->normalizeObject($item);
            yield $content;

            if ($deep && $content instanceof DirectoryAttributes) {
                foreach ($this->listContents($content->path(), true) as $deepItem) {
                    yield $deepItem;
                }
            }
        }
    }

    protected function normalizeObject(array $bunnyFileArray): StorageAttributes
    {
        return match ($bunnyFileArray['IsDirectory']) {
            true => new DirectoryAttributes(
                Util::normalizePath(
                    $this->replaceFirst(
                        $bunnyFileArray['StorageZoneName'] . '/',
                        '/',
                        $bunnyFileArray['Path'] . $bunnyFileArray['ObjectName']
                    )
                )
            ),
            default => new FileAttributes(
                Util::normalizePath(
                    $this->replaceFirst(
                        $bunnyFileArray['StorageZoneName'] . '/',
                        '/',
                        $bunnyFileArray['Path'] . $bunnyFileArray['ObjectName']
                    )
                ),
                $bunnyFileArray['Length'],
                Visibility::PUBLIC,
                self::parseBunnyTimestamp($bunnyFileArray['LastChanged']),
                $bunnyFileArray['ContentType'] ?: $this->detectMimeType($bunnyFileArray['Path'] . $bunnyFileArray['ObjectName']),
                $this->extractExtraMetadata($bunnyFileArray)
            ),
        };
    }

    protected function extractExtraMetadata(array $bunnyFileArray): array
    {
        return [
            'type' => $bunnyFileArray['IsDirectory'] ? 'dir' : 'file',
            'dirname' => Util::splitPathIntoDirectoryAndFile($bunnyFileArray['Path'])['dir'],
            'guid' => $bunnyFileArray['Guid'],
            'object_name' => $bunnyFileArray['ObjectName'],
            'timestamp' => self::parseBunnyTimestamp($bunnyFileArray['LastChanged']),
            'server_id' => $bunnyFileArray['ServerId'],
            'user_id' => $bunnyFileArray['UserId'],
            'date_created' => $bunnyFileArray['DateCreated'],
            'storage_zone_name' => $bunnyFileArray['StorageZoneName'],
            'storage_zone_id' => $bunnyFileArray['StorageZoneId'],
            'checksum' => $bunnyFileArray['Checksum'],
            'replicated_zones' => $bunnyFileArray['ReplicatedZones'],
        ];
    }

    /**
     * Detects the mime type from the provided file path
     */
    public function detectMimeType(string $path): string
    {
        try {
            $detector = new FinfoMimeTypeDetector();
            $mimeType = $detector->detectMimeTypeFromPath($path);

            if (! $mimeType) {
                return $detector->detectMimeTypeFromBuffer(stream_get_contents($this->readStream($path), 80));
            }

            return $mimeType;
        } catch (Exception) {
            return '';
        }
    }

    public function writeStream($path, $contents, Config $config): void
    {
        $this->write($path, stream_get_contents($contents), $config);
    }

    /**
     * @return resource
     *
     * @throws UnableToReadFile
     */
    public function readStream(string $path)
    {
        try {
            return $this->client->stream($path);
        } catch (Exceptions\BunnyCDNException|Exceptions\NotFoundException $exception) {
            throw UnableToReadFile::fromLocation($path, $exception->getMessage());
        }
    }

    /**
     * @throws UnableToDeleteDirectory
     * @throws FilesystemException
     */
    public function deleteDirectory(string $path): void
    {
        try {
            $this->client->delete(
                rtrim($path, '/') . '/'
            );
        } catch (Exceptions\BunnyCDNException $exception) {
            throw UnableToDeleteDirectory::atLocation($path, $exception->getMessage());
        }
    }

    /**
     * @throws UnableToCreateDirectory
     * @throws FilesystemException
     */
    public function createDirectory(string $path, Config $config): void
    {
        try {
            $this->client->makeDirectory($path);
        } catch (Exceptions\BunnyCDNException $exception) {
            match ($exception->getMessage()) {
                'Directory already exists' => '',
                default => throw UnableToCreateDirectory::atLocation($path, $exception->getMessage())
            };
        }
    }

    /**
     * @throws InvalidVisibilityProvided
     */
    public function setVisibility(string $path, string $visibility): void
    {
        throw UnableToSetVisibility::atLocation($path, 'BunnyCDN does not support visibility');
    }

    /**
     * @throws UnableToRetrieveMetadata
     */
    public function visibility(string $path): FileAttributes
    {
        try {
            return new FileAttributes($this->getObject($path)->path(), null, $this->pullZoneURL ? 'public' : 'private');
        } catch (UnableToReadFile|TypeError $exception) {
            throw new UnableToRetrieveMetadata($exception->getMessage());
        }
    }

    public function mimeType(string $path): FileAttributes
    {
        try {
            $object = $this->getObject($path);

            if (! $object->mimeType()) {
                $mimeType = $this->detectMimeType($path);

                if (! $mimeType || $mimeType === 'text/plain') { // Really not happy about this being required by Fly's Test case
                    throw new UnableToRetrieveMetadata('Unknown Mimetype');
                }

                return new FileAttributes(
                    $path,
                    null,
                    null,
                    null,
                    $mimeType
                );
            }

            return $object;
        } catch (UnableToReadFile $exception) {
            throw new UnableToRetrieveMetadata($exception->getMessage());
        } catch (TypeError) {
            throw new UnableToRetrieveMetadata('Cannot retrieve mimeType of folder');
        }
    }

    protected function getObject(string $path = ''): FileAttributes
    {
        $directory = pathinfo($path, PATHINFO_DIRNAME);
        $list = (new DirectoryListing($this->listContents($directory, false)))
            ->filter(function (FileAttributes|StorageAttributes $item) use ($path) {
                return Util::normalizePath($item->path()) === $path;
            })->toArray();

        if (count($list) === 1) {
            // @phpstan-ignore-next-line
            return $list[0];
        }

        if (count($list) > 1) {
            throw UnableToReadFile::fromLocation($path, 'More than one file was returned for path:"' . $path . '", contact package author.');
        }

        throw UnableToReadFile::fromLocation($path, 'Error 404:"' . $path . '"');
    }

    public function lastModified(string $path): FileAttributes
    {
        try {
            return $this->getObject($path);
        } catch (UnableToReadFile $exception) {
            throw new UnableToRetrieveMetadata($exception->getMessage());
        } catch (TypeError) {
            throw new UnableToRetrieveMetadata('Last Modified only accepts files as parameters, not directories');
        }
    }

    public function fileSize(string $path): FileAttributes
    {
        try {
            return $this->getObject($path);
        } catch (UnableToReadFile $exception) {
            throw new UnableToRetrieveMetadata($exception->getMessage());
        } catch (TypeError) {
            throw new UnableToRetrieveMetadata('Cannot retrieve size of folder');
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        try {
            $this->write($destination, $this->read($source), new Config());
            $this->delete($source);
        } catch (UnableToReadFile $exception) {
            throw new UnableToMoveFile($exception->getMessage());
        }
    }

    public function delete(string $path): void
    {
        try {
            $this->client->delete($path);
        } catch (Exceptions\BunnyCDNException $exception) {
            if (! str_contains($exception->getMessage(), '404')) {
                throw UnableToDeleteFile::atLocation($path, $exception->getMessage());
            }
        }
    }

    /**
     * @throws FilesystemException
     */
    public function directoryExists(string $path): bool
    {
        return $this->fileExists($path);
    }

    public function fileExists(string $path): bool
    {
        $list = new DirectoryListing($this->listContents(
            Util::splitPathIntoDirectoryAndFile($path)['dir'],
            false
        ));

        $count = $list->filter(function (StorageAttributes $item) use ($path) {
            return Util::normalizePath($item->path()) === Util::normalizePath($path);
        })->toArray();

        return (bool)count($count);
    }

    public function publicUrl(string $path, Config $config): string
    {
        if ($this->pullZoneURL === '') {
            throw new RuntimeException('In order to get a visible URL for a BunnyCDN object, you must pass the "pullZoneURL" parameter to the BunnyCDNAdapter.');
        }

        return rtrim($this->pullZoneURL, '/') . '/' . ltrim($path, '/');
    }

    protected static function parseBunnyTimestamp(string $timestamp): int
    {
        return (date_create_from_format('Y-m-d\TH:i:s.u', $timestamp) ?: date_create_from_format('Y-m-d\TH:i:s', $timestamp))->getTimestamp();
    }

    protected function replaceFirst(string $search, string $replace, string $subject): string
    {
        $position = strpos($subject, $search);

        if ($position !== false) {
            return (string)substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    public function checksum(string $path, Config $config): string
    {
        return $this->calculateChecksumFromStream($path, $config);
    }

    public function getUrl($path): string
    {
        return $this->publicUrl($path, new Config());
    }
}
