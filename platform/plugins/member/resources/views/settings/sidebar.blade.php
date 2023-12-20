<div class="col-12 col-md-3">
  <div class="list-group mb-3 br2" style="box-shadow: rgb(204, 204, 204) 0px 1px 1px;">
    <div class="list-group-item fw6 bn light-gray-text">
      {{ trans('plugins/member::dashboard.sidebar_title') }}
    </div>
    <a href="{{ route('public.member.settings') }}" class="list-group-item list-group-item-action bn @if (Route::currentRouteName() == 'public.member.settings') active @endif">
      <i class="fas fa-user-circle me-2"></i>
      <span>{{ trans('plugins/member::dashboard.sidebar_information') }}</span>
    </a>
    <a href="{{ route('public.member.security') }}" class="list-group-item list-group-item-action bn @if (Route::currentRouteName() == 'public.member.security') active @endif">
      <i class="fas fa-user-lock me-2"></i>
      <span>{{ trans('plugins/member::dashboard.sidebar_security') }}</span>
    </a>
  </div>
</div>
