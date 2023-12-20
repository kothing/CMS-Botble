<?php

namespace Botble\ACL\Traits;

use Illuminate\Support\Facades\Auth;

trait LogoutGuardTrait
{
    public function isActiveGuard($request, $guard): bool
    {
        Auth::guard($guard);

        $name = Auth::getName();

        return $this->sessionHas($request, $name) && $this->sessionGet(
            $request,
            $name
        ) === $this->getAuthIdentifier($guard);
    }

    public function sessionHas($request, $name): bool
    {
        return $request->session()->has($name);
    }

    public function sessionGet($request, $name)
    {
        return $request->session()->get($name);
    }

    public function getAuthIdentifier($guard)
    {
        return Auth::guard($guard)->id();
    }
}
