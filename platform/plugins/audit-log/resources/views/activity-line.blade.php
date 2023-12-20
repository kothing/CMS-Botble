<span class="log-icon log-icon-{{ $history->type }}"></span>
<span>
    @if ($history->user->id)
        <a href="{{ route('users.profile.view', $history->user->id) }}" class="d-inline-block">{{ $history->user->name }}</a>
    @else
        <span class="d-inline-block">{{ trans('plugins/audit-log::history.system') }}</span>
    @endif
    <span class="d-inline-block">
        @if (Lang::has('plugins/audit-log::history.' . $history->action)) {{ trans('plugins/audit-log::history.' . $history->action) }} @else {{ $history->action }} @endif
    </span>
    @if ($history->module)
        <span class="d-inline-block">
            @if (Lang::has('plugins/audit-log::history.' . $history->module)) {{ trans('plugins/audit-log::history.' . $history->module) }} @else {{ $history->module }} @endif
        </span>
    @endif
    @if ($history->reference_name)
        @if (empty($history->user) || $history->user->name != $history->reference_name)
            <span class="d-inline-block">
                "{{ Str::limit($history->reference_name, 40) }}"
            </span>
        @endif
    @endif
    .
</span>
<span class="small italic d-inline-block">{{ $history->created_at->diffForHumans() }} </span>
<span class="d-inline-block">(<a href="https://ipinfo.io/{{ $history->ip_address }}" target="_blank" title="{{ $history->ip_address }}" rel="nofollow">{{ $history->ip_address }}</a>)</span>
