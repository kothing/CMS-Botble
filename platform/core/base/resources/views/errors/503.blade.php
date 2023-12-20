<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="robots" content="noindex,nofollow,noarchive" />
    <title>{{ __('Maintenance mode') }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/core/core/base/css/error-pages.css') }}">
</head>
<body>
<div class="container">
    <h1>{{ __('Maintenance mode') }}</h1>
    <p>{{ __('Sorry, we are doing some maintenance. Please check back soon.') }}</p>
    <small>{!! BaseHelper::clean(__("If you are the administrator and you can't access your site after enabling maintenance mode, just need to delete file <strong>storage/framework/down</strong> to turn-off maintenance mode.")) !!}</small>
</div>
</body>
</html>

