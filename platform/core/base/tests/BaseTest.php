<?php

namespace Botble\Base\Tests;

use Botble\ACL\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class BaseTest extends TestCase
{
    public function testRoutes(): void
    {
        $this->withoutEvents();

        $auth = User::first();

        if ($auth) {
            $this->be($auth);
        }

        $routeCollection = Route::getRoutes();

        foreach ($routeCollection->getRoutes() as $value) {
            if (! in_array('GET', $value->methods())) {
                continue;
            }

            if (Str::contains($value->uri(), '_debugbar')) {
                continue;
            }

            $response = $this->call($value->getActionMethod(), $value->uri());

            $this->assertNotEquals(500, $response->status(), $value->getActionMethod() . ' ' . $value->uri());
        }

        /*$slugs = Slug::distinct('reference_type')->get();

        foreach ($slugs as $slug) {
            $url = url($slug->prefix . '/' . $slug->key);

            $response = $this->call('GET', $url);

            $this->assertNotEquals(500, $response->status(), $url);
        }*/
    }
}
