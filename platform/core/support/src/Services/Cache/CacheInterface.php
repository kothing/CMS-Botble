<?php

namespace Botble\Support\Services\Cache;

interface CacheInterface
{
    public function get(string $key);

    public function put(string $key, $value, $minutes = false);

    public function has(string $key): bool;

    public function flush(): bool;
}
