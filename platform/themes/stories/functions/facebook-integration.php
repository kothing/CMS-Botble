<?php

app()->booted(function () {
    // Facebook integration
    theme_option()
        ->setSection([
            'title' => __('Facebook Integration'),
            'desc' => __('Facebook Integration'),
            'id' => 'opt-text-subsection-facebook-integration',
            'subsection' => true,
            'icon' => 'fab fa-facebook',
        ])
        ->setField([
            'id' => 'facebook_chat_enabled',
            'section_id' => 'opt-text-subsection-facebook-integration',
            'type' => 'customSelect',
            'label' => __('Enable Facebook chat?'),
            'attributes' => [
                'name' => 'facebook_chat_enabled',
                'list' => [
                    'no' => trans('core/base::base.no'),
                    'yes' => trans('core/base::base.yes'),
                ],
                'value' => 'no',
                'options' => [
                    'class' => 'form-control',
                ],
            ],
            'helper' => __(
                'To show chat box on that website, please go to :link and add :domain to whitelist domains!',
                [
                    'domain' => Html::link(url('')),
                    'link' => Html::link('https://www.facebook.com/' . theme_option('facebook_page_id') . '/settings/?tab=messenger_platform'),
                ]
            ),
        ])
        ->setField([
            'id' => 'facebook_page_id',
            'section_id' => 'opt-text-subsection-facebook-integration',
            'type' => 'text',
            'label' => __('Facebook page ID'),
            'attributes' => [
                'name' => 'facebook_page_id',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                ],
            ],
            'helper' => __(
                'You can get fan page ID using this site :link',
                ['link' => Html::link('https://findidfb.com')]
            ),
        ])
        ->setField([
            'id' => 'facebook_comment_enabled_in_post',
            'section_id' => 'opt-text-subsection-facebook-integration',
            'type' => 'customSelect',
            'label' => __('Enable Facebook comment in post detail page?'),
            'attributes' => [
                'name' => 'facebook_comment_enabled_in_post',
                'list' => [
                    'yes' => trans('core/base::base.yes'),
                    'no' => trans('core/base::base.no'),
                ],
                'value' => 'no',
                'options' => [
                    'class' => 'form-control',
                ],
            ],
        ])
        ->setField([
            'id' => 'facebook_app_id',
            'section_id' => 'opt-text-subsection-facebook-integration',
            'type' => 'text',
            'label' => __('Facebook App ID'),
            'attributes' => [
                'name' => 'facebook_app_id',
                'value' => null,
                'options' => [
                    'class' => 'form-control',
                ],
                'placeholder' => 'Ex: 2061237023872679',
            ],
            'helper' => __(
                'You can create your app in :link',
                ['link' => Html::link('https://developers.facebook.com/apps')]
            ),
        ])
        ->setField([
            'id' => 'facebook_admins',
            'section_id' => 'opt-text-subsection-facebook-integration',
            'type' => 'repeater',
            'label' => __('Facebook Admins'),
            'attributes' => [
                'name' => 'facebook_admins',
                'value' => null,
                'fields' => [
                    [
                        'type' => 'text',
                        'label' => __('Facebook Admin ID'),
                        'attributes' => [
                            'name' => 'text',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                                'data-counter' => 40,
                            ],
                        ],
                    ],
                ],
            ],
            'helper' => __(
                'Facebook admins to manage comments :link',
                ['link' => Html::link('https://developers.facebook.com/docs/plugins/comments')]
            ),
        ]);

    add_filter(THEME_FRONT_HEADER, function ($html) {
        if (theme_option('facebook_app_id')) {
            $html .= Html::meta(null, theme_option('facebook_app_id'), ['property' => 'fb:app_id'])->toHtml();
        }

        if (theme_option('facebook_admins')) {
            foreach (json_decode(theme_option('facebook_admins'), true) as $facebookAdminId) {
                if (Arr::get($facebookAdminId, '0.value')) {
                    $html .= Html::meta(null, Arr::get($facebookAdminId, '0.value'), ['property' => 'fb:admins'])
                        ->toHtml();
                }
            }
        }

        return $html;
    }, 1180);

    add_filter(THEME_FRONT_FOOTER, function ($html) {
        return $html . Theme::partial('facebook-integration');
    }, 1180);
});
