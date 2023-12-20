<?php

namespace Botble\ACL\Traits;

trait RedirectsUsers
{
    public function redirectPath(): string
    {
        if (method_exists($this, 'redirectTo')) {
            return (string)$this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? (string)$this->redirectTo : '/';
    }
}
