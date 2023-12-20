<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="robots" content="noindex,nofollow,noarchive" />
    <title>{{ __('An Error Occurred: Internal Server Error') }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/core/core/base/css/error-pages.css') }}">
</head>
<body>
<div class="container">
    <h1>{{ __('Oops! An Error Occurred') }}</h1>
    <h2>{{ __('The server returned a "403 Forbidden".') }}</h2>

    <p>
        {{ __('Something is broken. Please let us know what you were doing when this error occurred. We will fix it as soon as possible. Sorry for any inconvenience caused.') }}
    </p>
</div>
</body>
</html>
