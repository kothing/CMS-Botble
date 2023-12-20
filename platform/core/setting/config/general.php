<?php

return [
    'driver' => env('CMS_SETTING_STORE_DRIVER', 'database'),
    'enable_email_smtp_settings' => env('CMS_ENABLE_EMAIL_SMTP_SETTINGS', true),
];
