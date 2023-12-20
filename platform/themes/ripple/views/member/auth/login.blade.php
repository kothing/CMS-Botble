@php Theme::layout('no-sidebar') @endphp
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-sm-10 auth-form-wrapper">
            <div class="card login-form">
                <div class="card-body">
              <h4 class="text-center">{{ trans('plugins/member::dashboard.login-title') }}</h4>
              <br>
            <form method="POST" action="{{ route('public.member.login') }}">
              @csrf
              <div class="form-group mb-3">
                  <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ trans('plugins/member::dashboard.email') }}" name="email" value="{{ old('email') }}" autofocus>
                  @if ($errors->has('email'))
                    <span class="invalid-feedback">
                <strong>{{ $errors->first('email') }}</strong>
                </span>
                  @endif
                </div>
              <div class="form-group mb-3">
                  <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ trans('plugins/member::dashboard.password') }}" name="password">
                  @if ($errors->has('password'))
                    <span class="invalid-feedback">
                <strong>{{ $errors->first('password') }}</strong>
                </span>
                  @endif
              </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"
                                           name="remember" @checked(old('remember', 0))> {{ trans('plugins/member::dashboard.remember-me') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('public.member.password.request') }}">
                                {{ trans('plugins/member::dashboard.forgot-password-cta') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-full fw6">
                        {{ trans('plugins/member::dashboard.login-cta') }}
                    </button>
                </div>

                <div class="form-group text-center">
                    <p>{{ __("Don't have an account?") }} <a href="{{ route('public.member.register') }}" class="d-block d-sm-inline-block text-sm-left text-center">{{ __('Register a new account') }}</a></p>
                </div>

                <div class="text-center">
                    {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Member\Models\Member::class) !!}
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>
