<?php

namespace Botble\Member\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfMember
{
    public function handle(Request $request, Closure $next, string $guard = 'member')
    {
        if (Auth::guard($guard)->check()) {
            return redirect(route('public.member.dashboard'));
        }

        return $next($request);
    }
}
