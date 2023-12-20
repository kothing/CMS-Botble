@if(! $isRendered)
    <script src="{{ $url }}" async defer></script>

    <script>
        'use strict';

        window.recaptchaInputs = window.recaptchaInputs || [];

        var refreshRecaptcha = function () {
            window.recaptchaInputs.forEach(function (item, index) {
                grecaptcha.reset(index);
            });
        };

        var onloadCallback = function () {
            window.recaptchaInputs.forEach(function (item) {
                grecaptcha.render(item);
            });
        };
    </script>
@endif

<script>window.recaptchaInputs.push('{{ $name }}');</script>
