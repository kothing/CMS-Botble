<?php

namespace Botble\Optimize\Http\Middleware;

class RemoveComments extends PageSpeed
{
    public function apply(string $buffer): string
    {
        $replace = [
            '/<!--[^]><!\[](.*?)[^\]]-->/s' => '',
        ];

        return $this->replace($replace, $buffer);
    }
}
