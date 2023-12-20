<?php

namespace Botble\Member\Forms\Fields;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FormField;
use Botble\Base\Supports\Editor;
use Illuminate\Support\Arr;

class CustomEditorField extends FormField
{
    protected function getTemplate(): string
    {
        return 'plugins/member::forms.fields.custom-editor';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true): string
    {
        (new Editor())->registerAssets();

        $options['attr'] = Arr::set($options['attr'], 'class', Arr::get($options['attr'], 'class') .
            ' form-control editor-' . BaseHelper::getRichEditor());

        return parent::render($options, $showLabel, $showField, $showError);
    }
}
