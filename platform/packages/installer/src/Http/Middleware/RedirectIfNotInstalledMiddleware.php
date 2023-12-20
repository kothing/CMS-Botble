<?php

namespace Botble\Installer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RedirectIfNotInstalledMiddleware extends InstallerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! $this->alreadyInstalled() && Route::current()->getPrefix() !== 'install') {
            return redirect()->route('installers.welcome');
        }

        return $next($request);
    }
}
