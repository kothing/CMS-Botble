@if (! $alreadyConsentedWithCookies)

    <div class="js-cookie-consent cookie-consent cookie-consent-{{ theme_option('cookie_consent_style', 'full-width') }}" style="background-color: {{ theme_option('cookie_consent_background_color', '#000') }} !important; color: {{ theme_option('cookie_consent_text_color', '#fff') }} !important;">
        <div class="cookie-consent-body" style="max-width: {{ theme_option('cookie_consent_max_width', 1170) }}px;">
            <span class="cookie-consent__message">
                {{ theme_option('cookie_consent_message', trans('plugins/cookie-consent::cookie-consent.message')) }}
                @if (theme_option('cookie_consent_learn_more_url') && theme_option('cookie_consent_learn_more_text'))
                    <a href="{{ route('public.single', theme_option('cookie_consent_learn_more_url')) }}">{{ theme_option('cookie_consent_learn_more_text') }}</a>
                @endif
            </span>

            <button class="js-cookie-consent-agree cookie-consent__agree" style="background-color: {{ theme_option('cookie_consent_background_color', '#000') }} !important; color: {{ theme_option('cookie_consent_text_color', '#fff') }} !important; border: 1px solid {{ theme_option('cookie_consent_text_color', '#fff') }} !important;">
                {{ theme_option('cookie_consent_button_text', trans('plugins/cookie-consent::cookie-consent.button_text')) }}
            </button>
        </div>
    </div>
    <div data-site-cookie-name="{{ $cookieConsentConfig['cookie_name'] ?? 'cookie_for_consent' }}"></div>
    <div data-site-cookie-lifetime="{{ $cookieConsentConfig['cookie_lifetime'] ?? 36000 }}"></div>
    <div data-site-cookie-domain="{{ config('session.domain') ?? request()->getHost() }}"></div>
    <div data-site-session-secure="{{ config('session.secure') ? ';secure' : null }}"></div>

@endif
