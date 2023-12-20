@extends('plugins/member::layouts.skeleton')
@section('content')
  <div class="dashboard crop-avatar">
    <div class="container">
      <div class="row">
        <div class="col-md-3 mb-3 dn db-ns">
          <div class="mb3">
            <div class="sidebar-profile">
              <div class="avatar-container mb-2">
                <div class="profile-image">
                  <div class="avatar-view mt-card-avatar mt-card-avatar-circle" style="max-width: 150px">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="br-100" style="width: 150px;">
                    <div class="mt-overlay br2">
                      <span><i class="fa fa-edit"></i></span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="f4 b">{{ $user->name }}</div>
              <div class="f6 mb3 light-gray-text">
                <i class="fas fa-envelope mr2"></i><a href="mailto:{{ $user->email }}" class="gray-text">{{ $user->email }}</a>
              </div>
              <div class="mb3">
                <div class="light-gray-text mb2">
                  <i class="fas fa-calendar-alt mr2"></i>{{ trans('plugins/member::dashboard.joined_on', ['date' => $user->created_at->format('F d, Y')]) }}
                </div>
                @if ($user->dob)
                  <div class="light-gray-text mb2">
                    <i class="fas fa-child mr2"></i>{{ trans('plugins/member::dashboard.dob', ['date' => $user->dob]) }}
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
          <div class="col-md-9 mb-3">
            {!! apply_filters(MEMBER_TOP_STATISTIC_FILTER, null) !!}
              @if (is_plugin_active('blog'))
                  @include('plugins/member::components.statistic')
              @endif
            <activity-log-component default-active-tab="activity-logs"></activity-log-component>
          </div>
      </div>
    </div>
    @include('plugins/member::modals.avatar')
  </div>
@endsection
