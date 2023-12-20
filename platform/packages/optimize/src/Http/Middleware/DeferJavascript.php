<?php

namespace Botble\Optimize\Http\Middleware;

class DeferJavascript extends PageSpeed
{
    public function apply(string $buffer): string
    {
        $replace = [
            '/<script(?=[^>]+src[^>]+)((?![^>]+defer|data-pagespeed-no-defer[^>]+)[^>]+)/i' => '<script $1 defer',
        ];

        return $this->replace($replace, $buffer);
    }
}
