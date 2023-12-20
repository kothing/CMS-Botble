<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @if (theme_option('favicon'))
        <link rel="shortcut icon" href="{{ RvMedia::getImageUrl(theme_option('favicon')) }}">
  @endif

  {!! SeoHelper::render() !!}

  {!! Assets::renderHeader(['core']) !!}

  {!! Html::style('/vendor/core/core/base/css/themes/default.css') !!}

  <!-- Styles -->
  <link href="{{ asset('vendor/core/plugins/member/css/app.css') }}" rel="stylesheet">

    @if ($isRTL = BaseHelper::isRtlEnabled())
        <link rel="stylesheet" href="{{ asset('vendor/core/core/base/css/rtl.css') }}">
    @endif

  <!-- Put translation key to translate in VueJS -->
  <script type="text/javascript">
      'use strict';

      window.trans = {{ Js::from(trans('plugins/member::dashboard')) }};
      var BotbleVariables = BotbleVariables || {};
      BotbleVariables.languages = {
        tables: {{ Js::from(trans('core/base::tables')) }},
        notices_msg: {{ Js::from(trans('core/base::notices')) }},
        pagination: {{ Js::from(trans('pagination')) }},
        system: {
          'character_remain': '{{ trans('core/base::forms.character_remain') }}'
        }
      };
      var RV_MEDIA_URL = {'media_upload_from_editor': '{{ route('public.member.upload-from-editor') }}'};
  </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body @if ($isRTL) dir="rtl" @endif>
  <div id="app">
    @include('plugins/member::components.header')
    <main class="pv3 pv4-ns">
      @yield('content')
    </main>
      @if (is_plugin_active('language'))
          @php
              $supportedLocales = Language::getSupportedLocales();
          @endphp

          @if ($supportedLocales && count($supportedLocales) > 1)
              @if (count(\Botble\Base\Supports\Language::getAvailableLocales()) > 1)
                  <footer>
                      <p>{{ __('Languages') }}:
                          @foreach ($supportedLocales as $localeCode => $properties)
                              <a rel="alternate" hreflang="{{ $localeCode }}" href="{{ route('settings.language', $localeCode) }}" @if ($localeCode == Language::getCurrentLocale()) class="active" @endif>
                                  {!! language_flag($properties['lang_flag'], $properties['lang_name']) !!}
                                  &nbsp;<span>{{ $properties['lang_name'] }}</span>
                              </a> &nbsp;
                          @endforeach
                      </p>
                  </footer>
              @endif
          @endif
      @endif
  </div>

  @if (session()->has('status') || session()->has('success_msg') || session()->has('error_msg') || (isset($errors) && $errors->any()) || isset($error_msg))
    <script type="text/javascript">
      window.noticeMessages = [];
      @if (session()->has('success_msg'))
      noticeMessages.push({'type': 'success', 'message': "{!! addslashes(session('success_msg')) !!}"});
      @endif
      @if (session()->has('status'))
      noticeMessages.push({'type': 'success', 'message': "{!! addslashes(session('status')) !!}"});
      @endif
      @if (session()->has('error_msg'))
      noticeMessages.push({'type': 'error', 'message': "{!! addslashes(session('error_msg')) !!}"});
      @endif
      @if (isset($error_msg))
      noticeMessages.push({'type': 'error', 'message': "{!! addslashes($error_msg) !!}"});
      @endif
      @if (isset($errors))
      @foreach ($errors->all() as $error)
      noticeMessages.push({'type': 'error', 'message': "{!! addslashes($error) !!}"});
        @endforeach
        @endif
    </script>
  @endif

  <!-- Scripts -->
  <script src="{{ asset('vendor/core/plugins/member/js/app.js') }}"></script>

  {!! Assets::renderFooter() !!}
  @stack('scripts')
  @stack('footer')
{!! apply_filters(THEME_FRONT_FOOTER, null) !!}
</body>
</html>
