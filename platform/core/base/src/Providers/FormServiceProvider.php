<?php

namespace Botble\Base\Providers;

use Botble\Base\Facades\Form;
use Botble\Base\Supports\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Form::component('mediaImage', 'core/base::forms.partials.image', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('mediaImages', 'core/base::forms.partials.images', [
            'name',
            'values' => [],
            'attributes' => [],
        ]);

        Form::component('mediaFile', 'core/base::forms.partials.file', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('modalAction', 'core/base::forms.partials.modal', [
            'name',
            'title',
            'type' => null,
            'content' => null,
            'action_id' => null,
            'action_name' => null,
            'modal_size' => null,
        ]);

        Form::component('helper', 'core/base::forms.partials.helper', ['content', 'icon']);

        Form::component('onOff', 'core/base::forms.partials.on-off', [
            'name',
            'value' => false,
            'attributes' => [],
        ]);

        /**
         * Custom checkbox
         * Every checkbox will not have the same name
         */
        Form::component('customCheckbox', 'core/base::forms.partials.custom-checkbox', [
            /**
             * @var array $values
             * @template: [
             *      [string $name, string $value, string $label, bool $selected, bool $disabled],
             *      [string $name, string $value, string $label, bool $selected, bool $disabled],
             *      [string $name, string $value, string $label, bool $selected, bool $disabled],
             * ]
             */
            'values',
        ]);

        /**
         * Custom radio
         * Every radio in list must have the same name
         */
        Form::component('customRadio', 'core/base::forms.partials.custom-radio', [
            /**
             * @var string $name
             */
            'name',
            /**
             * @var array $values
             * @template: [
             *      [string $value, string $label, bool $disabled],
             *      [string $value, string $label, bool $disabled],
             *      [string $value, string $label, bool $disabled],
             * ]
             */
            'values',
            /**
             * @var string|null $selected
             */
            'selected' => null,
        ]);

        Form::component('error', 'core/base::forms.partials.error', [
            'name',
            'errors' => null,
        ]);

        Form::component('editor', 'core/base::forms.partials.editor', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('ckeditor', 'core/base::forms.partials.ckeditor', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('tinymce', 'core/base::forms.partials.tinymce', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('customSelect', 'core/base::forms.partials.custom-select', [
            'name',
            'choices' => [],
            'selected' => null,
            'selectAttributes' => [],
            'optionsAttributes' => [],
            'optgroupsAttributes' => [],
        ]);

        Form::component('autocomplete', 'core/base::forms.partials.autocomplete', [
            'name',
            'choices' => [],
            'selected' => null,
            'selectAttributes' => [],
            'optionsAttributes' => [],
            'optgroupsAttributes' => [],
        ]);

        Form::component('googleFonts', 'core/base::forms.partials.google-fonts', [
            'name',
            'selected' => null,
            'selectAttributes' => [],
            'optionsAttributes' => [],
        ]);

        Form::component('customColor', 'core/base::forms.partials.color', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('repeater', 'core/base::forms.partials.repeater', [
            'name',
            'value' => null,
            'fields' => [],
            'attributes' => [],
        ]);

        Form::component('phoneNumber', 'core/base::forms.partials.phone-number', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('datePicker', 'core/base::forms.partials.date-picker', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);
    }
}
