@once
    <li class="dropdown dropdown-extended dropdown-inbox">
        <a href="{{ route('notifications.get-notification') }}" data-href="{{ route('notifications.update-notifications-count') }}" id="open-notification" class="dropdown-toggle dropdown-header-name">
            <input type="hidden" value="1" class="current-page">
            <i class="fas fa-bell"></i>
            @if ($countNotificationUnread)
                <span class="badge badge-default"> {{ $countNotificationUnread }} </span>
            @endif
        </a>
    </li>
@endonce

