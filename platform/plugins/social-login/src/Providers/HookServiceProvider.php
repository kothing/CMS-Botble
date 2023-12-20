<?php

namespace Botble\SocialLogin\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\SocialLogin\Facades\SocialService;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Arr;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (SocialService::setting('enable')) {
            add_filter(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, [$this, 'addLoginOptions'], 25, 2);
        }
    }

    public function addLoginOptions(string|null $html, string $module): string|null
    {
        if (! SocialService::isSupportedModule($module)) {
            return $html;
        }

        if ($total = count(SocialService::supportedModules())) {
            $params = [];
            $data = collect(SocialService::supportedModules())->firstWhere('model', $module);

            if ($total > 1) {
                $params = ['guard' => $data['guard']];
            }

            if (Arr::get($data, 'use_css', true) && defined('THEME_OPTIONS_MODULE_SCREEN_NAME')) {
                Theme::asset()
                    ->usePath(false)
                    ->add(
                        'social-login-css',
                        asset('vendor/core/plugins/social-login/css/social-login.css'),
                        [],
                        [],
                        '1.1.0'
                    );
            }

            $view = Arr::get($data, 'view', 'plugins/social-login::login-options');

            return $html . view($view, compact('params'))->render();
        }

        return $html;
    }
}
