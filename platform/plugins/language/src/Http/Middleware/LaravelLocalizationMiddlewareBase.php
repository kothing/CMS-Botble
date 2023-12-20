<?php

namespace Botble\Language\Http\Middleware;

use Illuminate\Http\Request;

class LaravelLocalizationMiddlewareBase
{
    /**
     * The URIs that should not be localized.
     */
    protected array $except = [];

    /**
     * Determine if the request has a URI that should not be localized.
     */
    protected function shouldIgnore(Request $request): bool
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
