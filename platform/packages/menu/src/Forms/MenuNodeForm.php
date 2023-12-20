<?php

namespace Botble\Menu\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Menu\Models\MenuNode;

class MenuNodeForm extends FormAbstract
{
    protected $template = 'core/base::forms.form-content-only';

    public function buildForm(): void
    {
        $this->setupModel(new MenuNode());

        $id = $this->model->id ?? 'new';

        $this
            ->withCustomFields()
            ->add('menu_id', 'hidden', [
                'attr' => [
                    'class' => 'menu_id',
                ],
                'value' => $this->request->route('menu'),
            ])
            ->add('title', 'text', [
                'label' => trans('packages/menu::menu.title'),
                'label_attr' => [
                    'class' => 'control-label',
                    'data-update' => 'title',
                    'for' => 'menu-node-title-' . $id,
                ],
                'attr' => [
                    'placeholder' => trans('packages/menu::menu.title_placeholder'),
                    'data-old' => $this->model->title,
                    'id' => 'menu-node-title-' . $id,
                ],
            ]);

        if (! $this->model->reference_id) {
            $this
                ->add('url', 'text', [
                    'label' => trans('packages/menu::menu.url'),
                    'label_attr' => [
                        'class' => 'control-label',
                        'data-update' => 'custom-url',
                        'for' => 'menu-node-url-' . $id,
                    ],
                    'attr' => [
                        'placeholder' => trans('packages/menu::menu.url_placeholder'),
                        'data-old' => $this->model->url,
                        'id' => 'menu-node-url-' . $id,
                    ],
                ]);
        }

        $this
            ->add('icon_font', 'text', [
                'label' => trans('packages/menu::menu.icon'),
                'label_attr' => [
                    'class' => 'control-label',
                    'data-update' => 'icon',
                    'for' => 'menu-node-icon-font-' . $id,
                ],
                'attr' => [
                    'placeholder' => trans('packages/menu::menu.icon_placeholder'),
                    'data-old' => $this->model->icon_font,
                    'id' => 'menu-node-icon-font-' . $id,
                ],
            ])
            ->add('css_class', 'text', [
                'label' => trans('packages/menu::menu.css_class'),
                'label_attr' => [
                    'class' => 'control-label',
                    'data-update' => 'css_class',
                    'for' => 'menu-node-css-class-' . $id,
                ],
                'attr' => [
                    'placeholder' => trans('packages/menu::menu.css_class_placeholder'),
                    'data-old' => $this->model->css_class,
                    'id' => 'menu-node-css-class-' . $id,
                ],
            ])
            ->add('target', 'customSelect', [
                'label' => trans('packages/menu::menu.target'),
                'label_attr' => [
                    'class' => 'control-label',
                    'data-update' => 'target',
                    'for' => 'menu-node-target-' . $id,
                ],
                'choices' => [
                    '_self' => trans('packages/menu::menu.self_open_link'),
                    '_blank' => trans('packages/menu::menu.blank_open_link'),
                ],
                'attr' => [
                    'data-old' => $this->model->target,
                    'id' => 'menu-node-target-' . $id,
                ],
            ]);
    }
}
