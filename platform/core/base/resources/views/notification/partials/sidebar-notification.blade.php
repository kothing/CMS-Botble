<a class="close-btn" id="close-notification">×</a>
@if (! isset($adminNotifications) || count($adminNotifications))
    <h2 class="title-notification-heading">{{ trans('core/base::notifications.notifications') }}</h2>
    <p class="action-notification">
        <a class="me-2 mark-read-all" href="{{ route('notifications.read-all-notification') }}">{{ trans('core/base::notifications.mark_as_read') }}</a>
        <span><a class="delete-all-notifications" href="{{ route('notifications.destroy-all-notification') }}">{{ trans('core/base::notifications.clear') }}</a></span>
    </p>
@endif
<ul class="list-group list-item-notification">
    @include('core/base::notification.partials.notification-item', ['notifications' => $adminNotifications ?? []])
</ul>

