<?php

namespace Botble\Base\Facades;

use Botble\Base\Supports\PageTitle as PageTitleSupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void setTitle(string $title)
 * @method static string|null getTitle(bool $full = true)
 *
 * @see \Botble\Base\Supports\PageTitle
 */
class PageTitle extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PageTitleSupport::class;
    }
}
