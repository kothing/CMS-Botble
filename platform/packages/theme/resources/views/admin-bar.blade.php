<nav id="admin_bar">
    <div class="admin-bar-container">
        <div class="admin-bar-logo">
            <a href="{{ route('dashboard.index') }}" title="{{ trans('packages/theme::theme.go_to_dashboard') }}">
                <img src="{{ setting('admin_logo') ? RvMedia::getImageUrl(setting('admin_logo')) : url(config('core.base.general.logo')) }}" alt="logo"/>
            </a>
        </div>
        <ul class="admin-navbar-nav">
            @foreach (AdminBar::getGroups() as $slug => $group)
                @if ($items = Arr::get($group, 'items', []))
                    @php ksort($items); @endphp
                    <li class="admin-bar-dropdown">
                        <a href="{{ Arr::get($group, 'link') }}" class="dropdown-toggle">
                            {{ Arr::get($group, 'title') }}
                        </a>
                        <ul class="admin-bar-dropdown-menu">
                            @foreach ($items as $item)
                                @continue(Arr::get($item, 'permission') && !Auth::user()->hasPermission($item['permission']))
                                <li>
                                    <a href="{{ Arr::get($item, 'link') }}">
                                        {{ Arr::get($item, 'title') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endforeach
            @if ($noGroups = AdminBar::getLinksNoGroup())
                @php ksort($noGroups) @endphp
                @foreach ($noGroups as $item)
                    @continue(Arr::get($item, 'permission') && ! Auth::user()->hasPermission($item['permission']))
                    <li>
                        <a href="{{ Arr::get($item, 'link') }}">{{ Arr::get($item, 'title') }}</a>
                    </li>
                @endforeach
            @endif
        </ul>
        <ul class="admin-navbar-nav admin-navbar-nav-right">
            <li class="admin-bar-dropdown">
                <a href="{{ route('users.profile.view', ['id' => Auth::id()]) }}" class="dropdown-toggle">
                    {{ Auth::user()->name }}
                </a>
                <ul class="admin-bar-dropdown-menu">
                    <li><a href="{{ route('users.profile.view', Auth::id()) }}">{{ trans('core/base::layouts.profile') }}</a></li>
                    <li><a href="{{ route('access.logout') }}">{{ trans('core/base::layouts.logout') }}</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
<script type="text/javascript">
    document.getElementsByTagName('body')[0].classList.add('show-admin-bar');
</script>
