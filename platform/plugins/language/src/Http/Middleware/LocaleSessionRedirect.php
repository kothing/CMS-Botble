<?php

namespace Botble\Language\Http\Middleware;

use Botble\Language\Facades\Language;
use Botble\Language\LanguageNegotiator;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleSessionRedirect extends LaravelLocalizationMiddlewareBase
{
    public function handle(Request $request, Closure $next)
    {
        // If the URL of the request is in exceptions.
        if ($this->shouldIgnore($request)) {
            return $next($request);
        }

        $params = array_filter(explode('/', $request->path()));

        if (count($params) > 0 && Language::checkLocaleInSupportedLocales($params[0])) {
            session(['language' => $params[0]]);

            app()->setLocale(session('language'));

            return $next($request);
        }

        $locale = session('language', false);

        if (! empty($params) && ! Language::checkLocaleInSupportedLocales($params[0])) {
            $locale = Language::getDefaultLocale();
        }

        if (
            empty($locale) &&
            empty($params) &&
            Language::hideDefaultLocaleInURL() &&
            Language::useAcceptLanguageHeader()
        ) {
            // When default locale is hidden and accept language header is true,
            // then compute browser language when no session has been set.
            // Once the session has been set, there is no need to negotiate language from browser again.
            $negotiator = new LanguageNegotiator(
                Language::getDefaultLocale(),
                Language::getSupportedLocales(),
                $request
            );

            $locale = $negotiator->negotiateLanguage();
        }

        session(['language' => Language::getDefaultLocale()]);
        app()->setLocale(session('language'));

        if (
            $locale &&
            Language::checkLocaleInSupportedLocales($locale) &&
            ! (Language::getDefaultLocale() === $locale &&
                Language::hideDefaultLocaleInURL())
        ) {
            app('session')->reflash();
            $redirection = Language::getLocalizedURL($locale, null, [], false);

            return new RedirectResponse($redirection, 302, ['Vary' => 'Accept-Language']);
        }

        return $next($request);
    }
}
