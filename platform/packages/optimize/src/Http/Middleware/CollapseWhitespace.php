<?php

namespace Botble\Optimize\Http\Middleware;

class CollapseWhitespace extends PageSpeed
{
    public function apply(string $buffer): string
    {
        $replace = [
            "/\n([\S])/" => '$1',
            "/\r/" => '',
            "/\n/" => '',
            "/\t/" => '',
            '/ +/' => ' ',
            '/> +</' => '><',
        ];

        $blocks = preg_split('/(<\/?pre[^>]*>)/', $buffer, -1, PREG_SPLIT_DELIM_CAPTURE);
        $buffer = '';
        foreach ($blocks as $i => $block) {
            if ($i % 4 == 2) {
                $buffer .= $block;
            } else {
                $buffer .= $this->replace($replace, $block);
            }
        }

        return $buffer;
    }
}
