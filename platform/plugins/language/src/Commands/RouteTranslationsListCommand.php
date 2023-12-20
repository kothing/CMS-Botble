<?php

namespace Botble\Language\Commands;

use Botble\Language\LanguageManager;
use Botble\Language\Traits\TranslatedRouteCommandContext;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Console\RouteListCommand;
use Symfony\Component\Console\Input\InputArgument;

class RouteTranslationsListCommand extends RouteListCommand
{
    use TranslatedRouteCommandContext;

    protected $name = 'route:trans:list';

    protected $description = 'List all registered routes for specific locales';

    public function handle(): int
    {
        $locale = $this->argument('locale');

        if (! $this->isSupportedLocale($locale)) {
            $this->error("Unsupported locale: '{$locale}'.");

            return self::FAILURE;
        }

        $this->loadFreshApplicationRoutes($locale);

        parent::handle();

        return self::SUCCESS;
    }

    protected function loadFreshApplicationRoutes(string $locale): void
    {
        $app = require $this->getBootstrapPath() . '/app.php';

        $key = LanguageManager::ENV_ROUTE_KEY;

        putenv("{$key}={$locale}");

        $app->make(Kernel::class)->bootstrap();

        putenv("{$key}=");

        $this->router = $app['router'];
    }

    protected function getArguments(): array
    {
        return [
            ['locale', InputArgument::REQUIRED, 'The locale to list routes for.'],
        ];
    }
}
