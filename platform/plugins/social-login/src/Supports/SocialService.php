<?php

namespace Botble\SocialLogin\Supports;

use Illuminate\Support\Arr;

class SocialService
{
    public function registerModule(array $model): self
    {
        config([
            'plugins.social-login.general.supported' => array_merge(
                $this->supportedModules(),
                [$model['guard'] => $model]
            ),
        ]);

        return $this;
    }

    public function supportedModules(): array
    {
        return (array) config('plugins.social-login.general.supported', []);
    }

    public function isSupportedModule(string $model): bool
    {
        return ! ! collect($this->supportedModules())->firstWhere('model', $model);
    }

    public function isSupportedModuleByKey(string $key): bool
    {
        return ! ! $this->getModule($key);
    }

    public function getModule(string $key): array|null
    {
        return Arr::get($this->supportedModules(), $key);
    }

    public function isSupportedGuard(string $guard): bool
    {
        return in_array($guard, array_keys($this->supportedModules()));
    }

    public function getEnvDisableData(): array
    {
        return ['demo'];
    }

    public function getDataDisable(string $key): string
    {
        $setting = $this->setting($key);

        if (! $setting) {
            return '';
        }

        return substr($setting, 0, 3) . '***' . substr($setting, -3, 3);
    }

    public function setting(string $key, bool $default = false): string
    {
        return (string)setting('social_login_' . $key, $default);
    }

    public function hasAnyProviderEnable(): bool
    {
        foreach ($this->getProviderKeys() as $value) {
            if ($this->getProviderEnabled($value)) {
                return true;
            }
        }

        return false;
    }

    public function getProviderKeys(): array
    {
        return array_keys($this->getProviders());
    }

    public function getProviders(): array
    {
        return [
            'facebook' => $this->getDataProviderDefault(),
            'google' => $this->getDataProviderDefault(),
            'github' => $this->getDataProviderDefault(),
            'linkedin' => $this->getDataProviderDefault(),
        ];
    }

    public function getDataProviderDefault(): array
    {
        return [
            'data' => [
                'app_id',
                'app_secret',
            ],
            'disable' => [
                'app_secret',
            ],
        ];
    }

    public function getProviderEnabled(string $provider): bool
    {
        return (bool)$this->setting($provider . '_enable');
    }

    public function getProviderKeysEnabled(): array
    {
        return collect($this->getProviderKeys())
            ->filter(function ($key) {
                return $this->getProviderEnabled($key);
            })
            ->toArray();
    }
}
