<?php

namespace Botble\Base\Forms\Fields;

use Botble\Base\Forms\FormField;

class OnOffField extends FormField
{
    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.on-off';
    }
}
