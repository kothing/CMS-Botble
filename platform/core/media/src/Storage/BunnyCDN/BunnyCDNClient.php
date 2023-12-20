<?php

namespace Botble\Media\Storage\BunnyCDN;

use Botble\Media\Storage\BunnyCDN\Exceptions\BunnyCDNException;
use Botble\Media\Storage\BunnyCDN\Exceptions\DirectoryNotEmptyException;
use Botble\Media\Storage\BunnyCDN\Exceptions\NotFoundException;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;

class BunnyCDNClient
{
    public Guzzle $client;

    public function __construct(protected string $storageZoneName, protected string $apiKey, protected string $region = BunnyCDNRegion::FALKENSTEIN)
    {
        $this->client = new Guzzle();
    }

    protected static function getBaseUrl($region): string
    {
        return match ($region) {
            BunnyCDNRegion::NEW_YORK => 'https://ny.storage.bunnycdn.com/',
            BunnyCDNRegion::LOS_ANGELES => 'https://la.storage.bunnycdn.com/',
            BunnyCDNRegion::SINGAPORE => 'https://sg.storage.bunnycdn.com/',
            BunnyCDNRegion::SYDNEY => 'https://syd.storage.bunnycdn.com/',
            BunnyCDNRegion::UNITED_KINGDOM => 'https://uk.storage.bunnycdn.com/',
            BunnyCDNRegion::STOCKHOLM => 'https://se.storage.bunnycdn.com/',
            default => 'https://storage.bunnycdn.com/'
        };
    }

    /**
     * @throws GuzzleException
     */
    protected function request(string $path, string $method = 'GET', array $options = []): mixed
    {
        $response = $this->client->request(
            $method,
            self::getBaseUrl($this->region) . Util::normalizePath('/' . $this->storageZoneName . '/') . $path,
            array_merge_recursive([
                'headers' => [
                    'Accept' => '*/*',
                    'AccessKey' => $this->apiKey, // Honestly... Why do I have to specify this twice... @BunnyCDN
                ],
            ], $options)
        );

        $contents = $response->getBody()->getContents();

        return json_decode($contents, true) ?? $contents;
    }

    /**
     * @param string $path
     * @return array
     *
     * @throws NotFoundException|BunnyCDNException
     */
    public function list(string $path): array
    {
        try {
            $listing = $this->request(Util::normalizePath($path) . '/');

            // Throw an exception if we don't get back an array
            if (! is_array($listing)) {
                throw new NotFoundException('File is not a directory');
            }

            return array_map(function ($bunnyCdnItem) {
                return $bunnyCdnItem;
            }, $listing);
        } catch (GuzzleException $exception) {
            throw match ($exception->getCode()) {
                404 => new NotFoundException($exception->getMessage()),
                default => new BunnyCDNException($exception->getMessage())
            };
        }
    }

    /**
     * @param string $path
     * @return string
     *
     * @throws BunnyCDNException
     * @throws NotFoundException
     */
    public function download(string $path): string
    {
        try {
            $content = $this->request($path . '?download');

            if (is_array($content)) {
                return json_encode($content);
            }

            return $content;
        } catch (GuzzleException $exception) {
            throw match ($exception->getCode()) {
                404 => new NotFoundException($exception->getMessage()),
                default => new BunnyCDNException($exception->getMessage())
            };
        }
    }

    /**
     * @param string $path
     * @return resource|null
     *
     * @throws BunnyCDNException
     * @throws NotFoundException
     */
    public function stream(string $path)
    {
        try {
            return $this->client->request(
                'GET',
                self::getBaseUrl($this->region) . Util::normalizePath('/' . $this->storageZoneName . '/') . $path,
                array_merge_recursive([
                    'stream' => true,
                    'headers' => [
                        'Accept' => '*/*',
                        'AccessKey' => $this->apiKey, // Honestly... Why do I have to specify this twice... @BunnyCDN
                    ],
                ])
            )->getBody()->detach();
        } catch (GuzzleException $exception) {
            throw match ($exception->getCode()) {
                404 => new NotFoundException($exception->getMessage()),
                default => new BunnyCDNException($exception->getMessage())
            };
        }
    }

    /**
     * @param string $path
     * @param $contents
     * @return mixed
     *
     * @throws BunnyCDNException
     */
    public function upload(string $path, $contents): mixed
    {
        try {
            return $this->request($path, 'PUT', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                ],
                'body' => $contents,
            ]);
        } catch (GuzzleException $exception) {
            $exception->getCode();

            throw new BunnyCDNException($exception->getMessage());
        }
    }

    /**
     * @param string $path
     * @return mixed
     *
     * @throws BunnyCDNException
     */
    public function makeDirectory(string $path): mixed
    {
        try {
            return $this->request(Util::normalizePath($path) . '/', 'PUT', [
                'headers' => [
                    'Content-Length' => 0,
                ],
            ]);
        } catch (GuzzleException $exception) {
            throw match ($exception->getCode()) {
                400 => new BunnyCDNException('Directory already exists'),
                default => new BunnyCDNException($exception->getMessage())
            };
        }
    }

    /**
     * @param string $path
     * @return mixed
     *
     * @throws NotFoundException
     * @throws DirectoryNotEmptyException|BunnyCDNException
     */
    public function delete(string $path): mixed
    {
        try {
            return $this->request($path, 'DELETE');
        } catch (GuzzleException $exception) {
            throw match ($exception->getCode()) {
                404 => new NotFoundException($exception->getMessage()),
                400 => new DirectoryNotEmptyException($exception->getMessage()),
                default => new BunnyCDNException($exception->getMessage())
            };
        }
    }
}
