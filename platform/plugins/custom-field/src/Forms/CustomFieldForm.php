<?php

namespace Botble\CustomField\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\CustomField\Facades\CustomField;
use Botble\CustomField\Http\Requests\CreateFieldGroupRequest;
use Botble\CustomField\Models\FieldGroup;
use Botble\CustomField\Repositories\Interfaces\FieldGroupInterface;

class CustomFieldForm extends FormAbstract
{
    public function __construct(protected FieldGroupInterface $fieldGroupRepository)
    {
        parent::__construct();
    }

    public function buildForm(): void
    {
        $customFieldItems = [];
        if ($this->getModel()) {
            $customFieldItems = $this->fieldGroupRepository->getFieldGroupItems($this->getModel()->id);
            $this->setActionButtons(view('plugins/custom-field::actions', ['object' => $this->getModel()])->render());
        }

        $this
            ->setupModel(new FieldGroup())
            ->setValidatorClass(CreateFieldGroupRequest::class)
            ->setFormOption('class', 'form-update-field-group')
            ->withCustomFields()
            ->add('title', 'text', [
                'label' => trans('core/base::forms.title'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'data-counter' => 120,
                ],
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'choices' => BaseStatusEnum::labels(),
            ])
            ->add('order', 'number', [
                'label' => trans('core/base::forms.order'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.order_by_placeholder'),
                ],
                'default_value' => 0,
            ])
            ->setBreakFieldPoint('status')
            ->addMetaBoxes([
                'rules' => [
                    'title' => trans('plugins/custom-field::base.form.rules.rules'),
                    'content' => view('plugins/custom-field::rules', [
                        'object' => $this->getModel(),
                        'customFieldItems' => json_encode($customFieldItems),
                        'rules_template' => CustomField::renderRules(),
                    ])->render(),
                ],
                'field-items-list' => [
                    'title' => trans('plugins/custom-field::base.form.field_items_information'),
                    'content' => view('plugins/custom-field::field-items-list')->render(),
                ],
            ]);
    }
}
