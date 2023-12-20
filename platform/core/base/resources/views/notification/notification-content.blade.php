@once
    <div id="admin-notification">
        <div id="notification-sidebar" class="sidebar show-notification-sidebar">
            <a class="close-btn" id="close-notification"><i class="fa fa-times"></i></a>

            <h2 class="title-notification-heading">{{ trans('core/base::notifications.notifications') }}</h2>
            <p class="action-notification" @if (isset($adminNotifications) && count($adminNotifications)) style="display: block" @endif>
                <a class="me-2 mark-read-all" href="{{ route('notifications.read-all-notification') }}">{{ trans('core/base::notifications.mark_as_read') }}</a>
                <span><a class="delete-all-notifications" href="{{ route('notifications.destroy-all-notification') }}">{{ trans('core/base::notifications.clear') }}</a></span>
            </p>
            <ul class="list-group list-item-notification"></ul>
        </div>
        <div class="has-loading" id="loading-notification" style="display: none;"><i class="fa fa-spinner fa-spin"></i></div>
    </div>

    <div id="sidebar-notification-backdrop"></div>
@endonce

