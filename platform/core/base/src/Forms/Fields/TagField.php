<?php

namespace Botble\Base\Forms\Fields;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormField;

class TagField extends FormField
{
    protected function getTemplate(): string
    {
        Assets::addStylesDirectly('vendor/core/base/libraries/tagify/tagify.css')
            ->addScriptsDirectly([
                'vendor/core/base/libraries/tagify/tagify.js',
                'vendor/core/base/js/tags.js',
            ]);

        return 'core/base::forms.fields.tags';
    }
}
