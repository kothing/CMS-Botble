<nav class="navbar navbar-expand-md navbar-light bg-white bb b--black-10">
  <div class="container">

        @if (theme_option('logo'))
          <a href="{{ route('public.index') }}"><img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}" height="35"></a>
        @else
          <div class="brand-container tc mr2 br2">
            <a class="navbar-brand b white ma0 pa0 dib w-100" href="{{ route('public.index') }}" title="{{ theme_option('site_title') }}">{{ ucfirst(mb_substr(theme_option('site_title'), 0, 1, 'utf-8')) }}</a>
          </div>
        @endif

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <!-- Right Side Of Navbar -->
      <ul class="navbar-nav ms-auto my-2">
        <!-- Authentication Links -->
        @if (!auth('member')->check())
          <li>
            <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" style="text-decoration: none; line-height: 32px;" href="{{ route('public.member.login') }}">
                <i class="fas fa-sign-in-alt"></i> {{ trans('plugins/member::dashboard.login-cta') }}
            </a>
          </li>
          <li>
            <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" style="text-decoration: none; line-height: 32px;" href="{{ route('public.member.register') }}">
                <i class="fas fa-user-plus"></i> {{ trans('plugins/member::dashboard.register-cta') }}
            </a>
          </li>
        @else
          <li>
            <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db mr2" style="text-decoration: none; line-height: 32px;" href="{{ route('public.member.dashboard') }}" title="{{ trans('plugins/member::dashboard.header_profile_link') }}">
              <span>
                <img src="{{ auth('member')->user()->avatar_url }}" class="br-100 v-mid mr1" style="width: 30px;">
                <span>{{ auth('member')->user()->name }}</span>
              </span>
            </a>
          </li>
          <li>
            <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db mr2" style="text-decoration: none; line-height: 32px;" href="{{ route('public.member.settings') }}" title="{{ trans('plugins/member::dashboard.header_settings_link') }}">
              <i class="fas fa-cogs mr1"></i>{{ trans('plugins/member::dashboard.header_settings_link') }}
            </a>
          </li>
          {!! apply_filters(MEMBER_TOP_MENU_FILTER, null) !!}
          @if (is_plugin_active('blog'))
              @include('plugins/member::components.menu')
          @endif
          <li>
            <a class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db" style="text-decoration: none; line-height: 32px;" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="{{ trans('plugins/member::dashboard.header_logout_link') }}">
              <i class="fas fa-sign-out-alt mr1"></i><span class="dn-ns">{{ trans('plugins/member::dashboard.header_logout_link') }}</span>
            </a>
            <form id="logout-form" action="{{ route('public.member.logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>
