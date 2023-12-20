<x-core-base::modal
    class="get-started-modal"
    size="lg"
    :options="['data-step' => 1]"
    :header="false"
    :footer="false"
    :title="false"
>
    <x-slot name="header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
    </x-slot>
    <div class="get-start-wrapper">
        <div class="text-center">
            <p><img src="{{ asset('vendor/core/packages/get-started/images/confetti.png') }}" alt="Icon">
            </p>
            <br>
            <h4>{{ trans('packages/get-started::get-started.welcome_title') }}</h4>
            <p>{{ trans('packages/get-started::get-started.welcome_description') }}</p>
            <br>
            <br>
            <form action="{{ route('get-started.save') }}" method="post">
                @csrf
                <input type="hidden" name="step" value="1">
                <button class="btn btn-primary btn-bigger"
                        type="submit">{{ trans('packages/get-started::get-started.get_started') }}</button>
            </form>
        </div>
    </div>
</x-core-base::modal>

<x-core-base::modal
    class="get-started-modal"
    size="lg"
    :options="['data-step' => 2]"
    :header="false"
    :footer="false"
    :title="false"
>
    <x-slot name="header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
    </x-slot>
    <div class="get-start-wrapper">
        <div>
            <h4>{{ trans('packages/get-started::get-started.customize_branding_title') }}</h4>
            <p>{{ trans('packages/get-started::get-started.customize_branding_description') }}</p>
            <form action="{{ route('get-started.save') }}" method="post">
                @csrf
                <input type="hidden" name="step" value="2">
                <div class="select-colors-fonts" data-select2-dropdown-parent="true">
                    <div class="row">
                        @if (theme_option()->hasField('primary_color'))
                            <div class="col-sm-6">
                                <h6>{{ trans('packages/get-started::get-started.colors') }}</h6>
                                <div class="form-group">
                                    <label
                                        for="primary-color">{{ trans('packages/get-started::get-started.primary_color') }}</label>
                                    {!! theme_option()->renderField(theme_option()->getField('primary_color')) !!}
                                </div>
                            </div>
                        @endif

                        @if (theme_option()->hasField('primary_font'))
                            <div class="col-sm-6">
                                <h6>{{ trans('packages/get-started::get-started.fonts') }}</h6>
                                <div class="form-group">
                                    <label
                                        for="primary-font">{{ trans('packages/get-started::get-started.primary_font') }}</label>
                                    {!! theme_option()->renderField(theme_option()->getField('primary_font')) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <h6>{{ trans('packages/get-started::get-started.identify') }}</h6>
                            <div class="form-group">
                                <label
                                    for="site-name">{{ trans('packages/get-started::get-started.site_title') }}</label>
                                <input type="text" name="site_title" class="form-control"
                                       id="site-name" value="{{ theme_option('site_title') }}"
                                       placeholder="{{ trans('packages/get-started::get-started.site_title') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label
                                    for="site-logo">{{ trans('packages/get-started::get-started.logo') }}</label>
                                {!! Form::mediaImage('logo', theme_option('logo'), ['allow_thumb' => false]) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label
                                    for="site-favicon">{{ trans('packages/get-started::get-started.favicon') }}</label>
                                {!! Form::mediaImage('favicon', theme_option('favicon'), ['allow_thumb' => false]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label
                                    for="admin-logo">{{ trans('packages/get-started::get-started.admin_logo') }}</label>
                                {!! Form::mediaImage('admin_logo', setting('admin_logo'), ['allow_thumb' => false, 'default_image' => url(config('core.base.general.logo'))]) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label
                                    for="admin-favicon">{{ trans('packages/get-started::get-started.admin_favicon') }}</label>
                                {!! Form::mediaImage('admin_favicon', setting('admin_favicon'), ['allow_thumb' => false, 'default_image' => url(config('core.base.general.favicon'))]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn btn-primary btn-bigger"
                            type="submit">{{ trans('packages/get-started::get-started.next_step') }} <i
                            class="fas fa-angle-double-right"></i></button>
                </div>
            </form>
        </div>
    </div>
</x-core-base::modal>

<x-core-base::modal
    class="get-started-modal"
    size="lg"
    :options="['data-step' => 3]"
    :header="false"
    :footer="false"
    :title="false"
>
    <x-slot name="header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
    </x-slot>
    <div class="get-start-wrapper" style="min-height: 0">
        <div>
            <h4>{{ trans('packages/get-started::get-started.change_default_account_info_title') }}</h4>
            <p>{{ trans('packages/get-started::get-started.change_default_account_info_description') }}</p>
            <form action="{{ route('get-started.save') }}" method="post">
                @csrf
                <input type="hidden" name="step" value="3">
                <div class="form-group">
                    <label
                        for="primary-color">{{ trans('packages/get-started::get-started.username') }}</label>
                    {!! Form::text('username', auth()->user()->username, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    <label
                        for="primary-color">{{ trans('packages/get-started::get-started.email') }}</label>
                    {!! Form::email('email', auth()->user()->email, ['class' => 'form-control']) !!}
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label
                                for="primary-color">{{ trans('packages/get-started::get-started.password') }}</label>
                            {!! Form::password('password', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label
                                for="primary-font">{{ trans('packages/get-started::get-started.password_confirmation') }}</label>
                            {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button class="btn btn-primary btn-bigger"
                            type="submit">{{ trans('packages/get-started::get-started.next_step') }} <i
                            class="fas fa-angle-double-right"></i></button>
                </div>
            </form>
        </div>
    </div>
</x-core-base::modal>

<x-core-base::modal
    class="get-started-modal"
    size="lg"
    :options="['data-step' => 4]"
    :header="false"
    :footer="false"
    :title="false"
>
    <x-slot name="header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
    </x-slot>
    <div class="get-start-wrapper" style="min-height: 0">
        <div>
            <div class="text-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="success-icon text-success">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h4 class="text-center">{{ trans('packages/get-started::get-started.site_ready_title') }}</h4>
            <p>{{ trans('packages/get-started::get-started.site_ready_description') }}</p>
            <br>
            <br>
            <form action="{{ route('get-started.save') }}" method="post">
                @csrf
                <input type="hidden" name="step" value="4">
                <div class="text-center">
                    <button class="btn btn-primary btn-bigger"
                            type="submit">{{ trans('packages/get-started::get-started.finish') }} <i
                            class="fas fa-angle-double-right"></i></button>
                </div>
            </form>
        </div>
    </div>
</x-core-base::modal>

<x-core-base::modal
    class="close-get-started-modal"
    size="lg"
    :header="false"
    :footer="false"
    :title="false"
>
    <x-slot name="header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
    </x-slot>
    <div class="get-start-wrapper">
        <div class="text-center">
            <h5>{{ trans('packages/get-started::get-started.exit_wizard_title') }}</h5>
            <br>
            <p>
                <button
                    class="btn btn-primary btn-bigger js-close-wizard">{{ trans('packages/get-started::get-started.exit_wizard_confirm') }}
                </button>&nbsp;&nbsp;
                <button
                    class="btn btn-primary btn-bigger btn-bordered js-back-to-wizard">{{ trans('packages/get-started::get-started.exit_wizard_cancel') }}
                </button>
            </p>
        </div>
    </div>
</x-core-base::modal>
