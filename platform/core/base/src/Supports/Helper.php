<?php

namespace Botble\Base\Supports;

use Botble\Base\Models\BaseModel;
use Botble\Base\Services\ClearCacheService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Throwable;

class Helper
{
    public static function autoload(string $directory): void
    {
        $helpers = File::glob($directory . '/*.php');
        foreach ($helpers as $helper) {
            File::requireOnce($helper);
        }
    }

    public static function handleViewCount(BaseModel $object, string $sessionName): bool
    {
        if (! array_key_exists($object->getKey(), session($sessionName, []))) {
            try {
                $object::withoutEvents(fn () => $object::withoutTimestamps(fn () => $object->increment('views')));
                session()->put($sessionName . '.' . $object->getKey(), time());

                return true;
            } catch (Throwable) {
                return false;
            }
        }

        return false;
    }

    public static function formatLog(array $input, string $line = '', string $function = '', string $class = ''): array
    {
        return array_merge($input, [
            'user_id' => Auth::check() ? Auth::id() : 'System',
            'ip' => Request::ip(),
            'line' => $line,
            'function' => $function,
            'class' => $class,
            'userAgent' => Request::header('User-Agent'),
        ]);
    }

    public static function removeModuleFiles(string $module, string $type = 'packages'): bool
    {
        $folders = [
            public_path('vendor/core/' . $type . '/' . $module),
            resource_path('assets/' . $type . '/' . $module),
            resource_path('views/vendor/' . $type . '/' . $module),
            lang_path('vendor/' . $type . '/' . $module),
            config_path($type . '/' . $module),
        ];

        foreach ($folders as $folder) {
            if (File::isDirectory($folder)) {
                File::deleteDirectory($folder);
            }
        }

        return true;
    }

    public static function isConnectedDatabase(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (Throwable) {
            return false;
        }
    }

    public static function clearCache(): bool
    {
        $clearCacheService = ClearCacheService::make();

        $clearCacheService->clearFrameworkCache();
        $clearCacheService->clearBootstrapCache();
        $clearCacheService->clearRoutesCache();
        $clearCacheService->clearPurifier();
        $clearCacheService->clearDebugbar();

        return true;
    }

    public static function getCountryNameByCode(string|null $countryCode): string|null
    {
        if (empty($countryCode)) {
            return null;
        }

        return Arr::get(self::countries(), $countryCode, $countryCode);
    }

    public static function getCountryCodeByName(string|null $countryName): string|null
    {
        if (empty($countryName)) {
            return null;
        }

        $found = array_filter(self::countries(), function ($item) use ($countryName) {
            return $item == $countryName;
        });

        if (! $found) {
            return null;
        }

        return Arr::first(array_keys($found));
    }

    public static function countries(): array
    {
        return config('core.base.general.countries', []);
    }

    public static function getIpFromThirdParty(): bool|string|null
    {
        $defaultIpAddress = Request::ip() ?: '127.0.0.1';

        try {
            $ip = trim(Http::withoutVerifying()->get('https://ipecho.net/plain')->body());

            return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : $defaultIpAddress;
        } catch (Throwable) {
            return $defaultIpAddress;
        }
    }

    public static function isIniValueChangeable(string $setting): bool
    {
        static $iniAll;

        if (! isset($iniAll)) {
            $iniAll = false;
            // Sometimes `ini_get_all()` is disabled via the `disable_functions` option for "security purposes".
            if (function_exists('ini_get_all')) {
                $iniAll = ini_get_all();
            }
        }

        // Bit operator to workaround https://bugs.php.net/bug.php?id=44936 which changes access level to 63 in PHP 5.2.6 - 5.2.17.
        if (isset($iniAll[$setting]['access']) && (INI_ALL === ($iniAll[$setting]['access'] & 7) || INI_USER === ($iniAll[$setting]['access'] & 7))) {
            return true;
        }

        // If we were unable to retrieve the details, fail gracefully to assume it's changeable.
        if (! is_array($iniAll)) {
            return true;
        }

        return false;
    }

    public static function convertHrToBytes(string|float|int|null $value): float|int
    {
        $value = strtolower(trim($value));
        $bytes = (int)$value;

        if (str_contains($value, 'g')) {
            $bytes *= 1024 * 1024 * 1024;
        } elseif (str_contains($value, 'm')) {
            $bytes *= 1024 * 1024;
        } elseif (str_contains($value, 'k')) {
            $bytes *= 1024;
        }

        // Deal with large (float) values which run into the maximum integer size.
        return min($bytes, PHP_INT_MAX);
    }
}
