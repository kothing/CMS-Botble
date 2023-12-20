<?php

namespace Botble\Base\Forms\Fields;

class CustomSelectField extends SelectType
{
    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.custom-select';
    }
}
