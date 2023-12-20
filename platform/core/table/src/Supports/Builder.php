<?php

namespace Botble\Table\Supports;

use Illuminate\Support\HtmlString;
use Yajra\DataTables\Html\Builder as BaseBuilder;

class Builder extends BaseBuilder
{
    public function scripts($script = null, array $attributes = ['type' => 'text/javascript']): HtmlString
    {
        $script = $script ?: $this->generateScripts();
        $attributes = $this->html->attributes($attributes);

        return new HtmlString('<script' . $attributes . '>$(document).ready(function() { ' . $script . ' });</script>' . "\n");
    }
}
