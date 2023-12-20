@if(! $isRendered)
    <script src="{{ $url }}" async defer></script>

    <script>
        'use strict';

        window.recaptchaInputs = window.recaptchaInputs || [];

        var refreshRecaptcha = function () {
            window.recaptchaInputs.forEach(function (item) {
                grecaptcha.execute('{{ $siteKey }}', {action: item.action}).then(function (token) {
                    var input = document.getElementById(item.id);

                    if (input) {
                        input.value = token;
                    }
                });
            });
        };

        var onloadCallback = function () {
            grecaptcha.ready(function () {
                refreshRecaptcha();
            });
        };
    </script>
@endif

<script>
    window.recaptchaInputs.push({ id: '{{ $id }}', action: '{{ $action }}' });
</script>
