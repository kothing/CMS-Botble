<?php

namespace Botble\Member\Forms;

use Botble\Blog\Forms\PostForm as BasePostForm;
use Botble\Blog\Models\Post;
use Botble\Member\Forms\Fields\CustomEditorField;
use Botble\Member\Forms\Fields\CustomImageField;
use Botble\Member\Http\Requests\PostRequest;

class PostForm extends BasePostForm
{
    public function buildForm(): void
    {
        parent::buildForm();

        if (! $this->formHelper->hasCustomField('customEditor')) {
            $this->formHelper->addCustomField('customEditor', CustomEditorField::class);
        }

        if (! $this->formHelper->hasCustomField('customImage')) {
            $this->formHelper->addCustomField('customImage', CustomImageField::class);
        }

        $tags = null;

        if ($this->getModel()) {
            $tags = $this->getModel()->tags()->pluck('name')->all();
            $tags = implode(',', $tags);
        }

        $this
            ->setupModel(new Post())
            ->setFormOption('template', 'plugins/member::forms.base')
            ->setFormOption('enctype', 'multipart/form-data')
            ->setValidatorClass(PostRequest::class)
            ->setActionButtons(view('plugins/member::forms.actions')->render())
            ->remove('status')
            ->remove('is_featured')
            ->remove('content')
            ->addAfter('description', 'content', 'customEditor', [
                'label' => trans('core/base::forms.content'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'rows' => 4,
                ],
            ])
            ->modify('tag', 'tags', [
                'label' => trans('plugins/blog::posts.form.tags'),
                'label_attr' => ['class' => 'control-label'],
                'value' => $tags,
                'attr' => [
                    'placeholder' => trans('plugins/blog::base.write_some_tags'),
                    'data-url' => route('public.member.tags.all'),
                ],
            ], true);

        $this->setBreakFieldPoint('categories[]');
    }
}
