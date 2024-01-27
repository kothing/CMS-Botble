<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="robots" content="noindex,nofollow,noarchive" />
    <title>{{ __('Maintenance mode') }}</title>
    <style>
        body {
            background-color: #fff;
            color: #222;
            font: 16px/1.5 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
        }
        .container {
            margin: 30px;
            max-width: 600px;
        }
        h1 {
            color: #dc3545;
            font-size: 24px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>{{ __('Maintenance mode') }}</h1>
    <p>{{ __('Sorry, we are doing some maintenance. Please check back soon.') }}</p>
    <small>{!! BaseHelper::clean(__("If you are the administrator and you can't access your site after enabling maintenance mode, just need to delete file <strong>storage/framework/down</strong> to turn-off maintenance mode.")) !!}</small>
    @if (get_admin_email()->first())
        <p>{!! BaseHelper::clean(__('If you need help, contact us at :mail.', ['mail' => Html::link('mailto:' . get_admin_email()->first(), get_admin_email()->first())])) !!}</p>
    @endif
</div>
</body>
</html>

