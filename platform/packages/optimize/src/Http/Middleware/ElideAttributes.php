<?php

namespace Botble\Optimize\Http\Middleware;

class ElideAttributes extends PageSpeed
{
    public function apply(string $buffer): string
    {
        $replace = [
            '/ method=("get"|get)/' => '',
            '/ disabled=[^ >]*(.*?)/' => ' disabled',
            '/ selected=[^ >]*(.*?)/' => ' selected',
        ];

        return $this->replace($replace, $buffer);
    }
}
