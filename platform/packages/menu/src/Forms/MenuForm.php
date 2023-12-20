<?php

namespace Botble\Menu\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\Menu\Http\Requests\MenuRequest;
use Botble\Menu\Models\Menu;

class MenuForm extends FormAbstract
{
    public function buildForm(): void
    {
        Assets::addScriptsDirectly([
            'vendor/core/packages/menu/libraries/jquery-nestable/jquery.nestable.js',
            'vendor/core/packages/menu/js/menu.js',
        ])
            ->addStylesDirectly([
                'vendor/core/packages/menu/libraries/jquery-nestable/jquery.nestable.css',
                'vendor/core/packages/menu/css/menu.css',
            ]);

        $locations = [];

        if ($this->getModel()) {
            $locations = $this->getModel()->locations()->pluck('location')->all();
        }

        $this
            ->setupModel(new Menu())
            ->setFormOption('class', 'form-save-menu')
            ->withCustomFields()
            ->setValidatorClass(MenuRequest::class)
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'choices' => BaseStatusEnum::labels(),
            ])
            ->addMetaBoxes([
                'structure' => [
                    'wrap' => false,
                    'content' => view('packages/menu::menu-structure', [
                        'menu' => $this->getModel(),
                        'locations' => $locations,
                    ])->render(),
                ],
            ])
            ->setBreakFieldPoint('status');
    }
}
