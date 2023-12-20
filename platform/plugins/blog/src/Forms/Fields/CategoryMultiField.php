<?php

namespace Botble\Blog\Forms\Fields;

use Botble\Base\Forms\FormField;

class CategoryMultiField extends FormField
{
    protected function getTemplate(): string
    {
        return 'plugins/blog::categories.categories-multi';
    }
}
