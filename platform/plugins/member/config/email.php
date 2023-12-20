<?php

return [
    'name' => 'plugins/member::member.settings.email.title',
    'description' => 'plugins/member::member.settings.email.description',
    'templates' => [
        'confirm-email' => [
            'title' => 'Confirm email',
            'description' => 'Send email to user when they register an account to verify their email',
            'subject' => 'Confirm Email Notification',
            'can_off' => false,
            'variables' => [
                'verify_link' => 'Verify email link',
            ],
        ],
        'password-reminder' => [
            'title' => 'Reset password',
            'description' => 'Send email to user when requesting reset password',
            'subject' => 'Reset Password',
            'can_off' => false,
            'variables' => [
                'reset_link' => 'Reset password link',
            ],
        ],
        'new-pending-post' => [
            'title' => 'New pending post',
            'description' => 'Send email to admin when a new post created',
            'subject' => 'New post is pending on {{ site_title }} by {{ post_author }}',
            'can_off' => true,
            'variables' => [
                'post_author' => 'Post Author',
                'post_name' => 'Post Name',
                'post_url' => 'Post URL',
            ],
        ],
    ],
];
