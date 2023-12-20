<input type="hidden" value="{{ $numberPages ?? 1 }}" class="number-page">
<input type="hidden" value="1" class="current-page">
<i class="fas fa-bell"></i>
@if ($countNotificationUnread > 0)
    <span class="badge badge-default"> {{ $countNotificationUnread }} </span>
@endif
