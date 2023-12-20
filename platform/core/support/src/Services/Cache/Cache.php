<?php

namespace Botble\Support\Services\Cache;

use Botble\Base\Facades\BaseHelper;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class Cache implements CacheInterface
{
    public function __construct(
        protected CacheManager $cache,
        protected string|null $cacheGroup,
        protected array $config = []
    ) {
        $this->config = ! empty($config) ? $config : [
            'cache_time' => setting('cache_time', 10) * 60,
            'stored_keys' => storage_path('cache_keys.json'),
        ];
    }

    public function get(string $key)
    {
        if (! file_exists($this->config['stored_keys'])) {
            return null;
        }

        return $this->cache->get($this->generateCacheKey($key));
    }

    public function generateCacheKey(string $key): string
    {
        return md5($this->cacheGroup) . $key;
    }

    public function put(string $key, $value, $minutes = false): bool
    {
        if (! $minutes) {
            $minutes = $this->config['cache_time'];
        }

        $key = $this->generateCacheKey($key);

        $this->storeCacheKey($key);

        $this->cache->put($key, $value, $minutes);

        return true;
    }

    public function storeCacheKey(string $key): bool
    {
        if (File::exists($this->config['stored_keys'])) {
            $cacheKeys = BaseHelper::getFileData($this->config['stored_keys']);
            if (! empty($cacheKeys) && ! in_array($key, Arr::get($cacheKeys, $this->cacheGroup, []))) {
                $cacheKeys[$this->cacheGroup][] = $key;
            }
        } else {
            $cacheKeys = [];
            $cacheKeys[$this->cacheGroup][] = $key;
        }

        BaseHelper::saveFileData($this->config['stored_keys'], $cacheKeys);

        return true;
    }

    public function has(string $key): bool
    {
        if (! File::exists($this->config['stored_keys'])) {
            return false;
        }

        $key = $this->generateCacheKey($key);

        return $this->cache->has($key);
    }

    public function flush(): bool
    {
        $cacheKeys = [];
        if (File::exists($this->config['stored_keys'])) {
            $cacheKeys = BaseHelper::getFileData($this->config['stored_keys']);
        }

        if (! empty($cacheKeys) && $caches = Arr::get($cacheKeys, $this->cacheGroup)) {
            foreach ($caches as $cache) {
                $this->cache->forget($cache);
            }

            unset($cacheKeys[$this->cacheGroup]);
        }

        if (! empty($cacheKeys)) {
            BaseHelper::saveFileData($this->config['stored_keys'], $cacheKeys);
        } elseif (File::exists($this->config['stored_keys'])) {
            File::delete($this->config['stored_keys']);
        }

        return true;
    }
}
